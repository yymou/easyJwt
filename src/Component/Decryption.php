<?php

namespace EasyJwt\Component;

use EasyJwt\Util\Single;
use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\TokenEncoded;
use Nowakowskir\JWT\Validation;

/**
 * 解密模型
 * @author yangyanlei
 * @email 875167485@qq.com
 * @date 2022/8/5
 */
class Decryption
{
    use Single;

    private $key = '';

    private $decodeObj;

    //设置秘钥
    public function setKey(string $key) : void
    {
        $this->key = $key ?? '';
    }

    //获取payload
    public function getPayload() : array
    {
        return $this->decodeObj->getPayload() ?? [];
    }

    //获取header
    public function getHeader() : array
    {
        return $this->decodeObj->getHeader() ?? [];
    }

    /**
     * 获取秘钥
     * @return void
     */
    public function getKey(string $algorithm) : void
    {
        if (empty($this->key)) {
            $encrptionModel = Encryption::getInstance($algorithm);
            if ($encrptionModel->getAlgorithmType() == $encrptionModel::OPENSSL) {
                $this->key = $encrptionModel->getPublicKey();
            } else {
                $this->key = $encrptionModel->getHmacKey();
            }
        }
    }

    /**
     * 验证token
     * @param string $token
     * @return array
     */
    public function verifyToken(string $token) : void
    {
        $tokenEncoded = new TokenEncoded($token);
        //读取token中得header
        $this->decodeObj = $tokenEncoded->decode();
        $header = $this->getHeader();
        if (empty($header)) {
            throw new \Exception("token error");
        }
        //校验算法合法性
        Validation::checkAlgorithmSupported($header['alg']);
        $this->getKey($header['alg']);
        //验证是否安全
        try {
            $tokenEncoded->validate($this->key, $header['alg']);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
