<?php

namespace EasyJwt;

use EasyJwt\Component\EncryptionKey;
use EasyJwt\Component\Payload;
use EasyJwt\Util\File;
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

    public function __construct()
    {
        //定义路径常量
        defined('EASYJWT_ROOT') or define('EASYJWT_ROOT', realpath(getcwd()));
        //定义当前时间常量
        defined('EASYJWT_NOW_TIME') or define('EASYJWT_NOW_TIME', time());
    }

    /**
     * 添加payload值
     * @param array $payload
     * @return void
     */
    public function setPayload(array $payload) : void
    {
        if (!empty($payload)) {
            $this->payload = array_merge($this->payload, $payload);
        }
    }

    //设置秘钥
    public function setKey(string $key) : void
    {
        $this->key = $key ?? '';
    }

    //获取key
    private function getKey() : void
    {
        if (empty($this->key)) {
            //生成key
            $this->key = (new EncryptionKey($this->algorithm))->get();
        }
    }

    /**
     * 设置支持的算法
     * @param string $algorithm
     * @return void
     * @throws \Nowakowskir\JWT\Exceptions\InsecureTokenException
     * @throws \Nowakowskir\JWT\Exceptions\UnsupportedAlgorithmException
     */
    public function setAlgorithm(string $algorithm) : void
    {
        //校验算法合法性
        Validation::checkAlgorithmSupported($algorithm);
        $this->algorithm = $algorithm ?? '';
    }

    /**
     * 获取token
     * @return void
     */
    public function getToken(array $info) : string
    {
        //设置基本的payload
        $this->setPayload(get_object_vars (new Payload()));
        if (empty($info)) {
            throw new \Exception("给点内容在请求吧");
        }
        $this->setPayload($info);
        //如果不存在秘钥的话 则生成相关的秘钥
        $this->getKey();
        $this->generateToken();
        return $this->token;
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
     * 验证token是否有效
     * @param string $token
     * @return array
     */
    public function verifyToken(string $token) : array
    {
        $tokenEncoded = new TokenEncoded($token);
        $this->getKey();
        //验证是否安全
        try {
            $tokenEncoded->validate($this->key, $this->algorithm);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $header = $tokenEncoded->decode()->getPayload();
        return $header;
    }
}