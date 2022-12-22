<?php

namespace EasyJwt;

use EasyJwt\Component\Decryption;
use EasyJwt\Component\Encryption;
use EasyJwt\Component\Payload;
use Nowakowskir\JWT\TokenDecoded;
use Nowakowskir\JWT\TokenEncoded;
use Nowakowskir\JWT\Validation;

/**
 * 入口文件
 * @author yangyanlei
 * @email 875167485@qq.com
 * @date 2022/7/29
 */
class Jwt
{
    //设置默认的算法
    private $algorithm = "RS256";

    //定义payload初始值
    private $payload = [];

    //token值
    private $token = "";

    //秘钥
    private $key;
    //私钥
    private $privateKey = '';
    //公钥
    private $publicKey = '';
    
    public function __construct($algorithm = '')
    {
        if (!empty($algorithm)) {
            $this->setAlgorithm($algorithm);
        }
        //定义路径常量
        defined('EASYJWT_ROOT') or define('EASYJWT_ROOT', dirname(__FILE__));
        //定义当前时间常量
        defined('EASYJWT_NOW_TIME') or define('EASYJWT_NOW_TIME', time());
    }

    /**
     * 添加payload值
     * @param array $payload
     * @return void
     */
    public function setPayload(array $payload) : Jwt
    {
        if (!empty($payload)) {
            $this->payload = array_merge($payload, $this->payload);
        }
        return $this;
    }

    //设置秘钥
    public function setKey(string $key) : Jwt
    {
        $this->key = $key;
        return $this;
    }

    /**
     * 设置超时时间
     * @param $expireTime
     * @return void
     */
    public function setExp($expireTime = Config::EXPIRE_TIME) : Jwt
    {
        $this->setPayload(['exp' => EASYJWT_NOW_TIME + $expireTime]);
        return $this;
    }

    /**
     * 设置支持的算法
     * @param string $algorithm
     * @return void
     * @throws \Nowakowskir\JWT\Exceptions\InsecureTokenException
     * @throws \Nowakowskir\JWT\Exceptions\UnsupportedAlgorithmException
     */
    private function setAlgorithm(string $algorithm) : Jwt
    {
        //校验算法合法性
        Validation::checkAlgorithmSupported($algorithm);
        $this->algorithm = $algorithm ?? '';
        return $this;
    }

    /**
     * 获取token
     * @return void
     */
    public function getToken() : string
    {
        //判断payload
        if (empty($this->payload)) {
            throw new \Exception("please set payload");
        }
        //设置基本的payload
        $this->setPayload(get_object_vars(new Payload()));
        //如果不存在秘钥的话 则生成相关的秘钥
        $this->getKey();
        $this->generateToken();
        return $this->token;
    }


    //获取key
    private function getKey() : void
    {
        if (empty($this->key)) {
            $encryptionObj = Encryption::getInstance($this->algorithm);
            if ($encryptionObj->getAlgorithmType() == $encryptionObj::OPENSSL) {
                $this->key = $encryptionObj->getPrivateKey();
            } else {
                $this->key = $encryptionObj->getHmacKey();
            }
        }
    }

    //生成token
    private function generateToken()
    {
        try {
            $tokenDecoded = new TokenDecoded($this->payload);
            $tokenEncoded = $tokenDecoded->encode($this->key, $this->algorithm);
            $this->token = $tokenEncoded->toString();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 解析token
     * @param string $token
     * @param string $key 秘钥
     * @return void
     * @throws \Exception
     */
    public function explainToken(string $token, string $key = '') : Jwt
    {
        if (empty($token)) {
            throw new \Exception("token can not empty");
        }
        Decryption::getInstance()->setKey($key);
        Decryption::getInstance()->verifyToken($token);
        return $this;
    }

    /**
     * 获取payload
     * @return array
     */
    public function getPayload() : array
    {
        return Decryption::getInstance()->getPayload() ?? [];
    }

    /**
     * 获取header
     * @return array
     */
    public function getHeader() : array
    {
        return Decryption::getInstance()->getHeader() ?? [];
    }
}