<?php

namespace EasyJwt\Component;

use EasyJwt\Util\File;
use EasyJwt\Util\Single;
use Nowakowskir\JWT\JWT;

/**
 * 加密模型
 * @author yangyanlei
 * @email 875167485@qq.com
 * @date 2022/7/29
 */
class Encryption
{
    use Single;

    //定义加密方法
    private $func;

    //定义加密类型
    private $type;

    private static $privateKey;

    private static $publicKey;

    private static $hmacKey;

    const HASH_HAMC = 'hash_hmac';
    const OPENSSL = 'openssl';

    public function __construct(string $algorithm)
    {
        list($this->func, $this->type) = JWT::ALGORITHMS[$algorithm];
        $this->checkKey();
    }

    //获取加密类型
    public function getAlgorithmType() : string
    {
        switch ($this->func) {
            case 'hash_hmac' :
                return self::HASH_HAMC;
            case 'openssl' :
                return self::OPENSSL;
            default:
                throw new \Exception('Unsupported algorithm type');
        }
    }

    //获取私钥
    public function getPrivateKey() : string
    {
        return self::$privateKey ?? '';
    }

    //获取公钥
    public function getPublicKey() : string
    {
        return self::$publicKey ?? '';
    }

    //获取hmac秘钥
    public function getHmacKey() : string
    {
        return self::$hmacKey ?? '';
    }

    /**
     * 生成秘钥
     * @return string
     */
    private function checkKey() : void
    {
        if ($this->getAlgorithmType() == self::HASH_HAMC) {
            if (file_exists(FILE::SECRET_DIR . "/" . FILE::HMAC_KEY)) {
                self::$hmacKey =  file_exists(FILE::SECRET_DIR . "/" . FILE::HMAC_KEY);
            } else {
                self::$hmacKey = uniqid(rand());
                File::writeFile(FILE::SECRET_DIR, FILE::HMAC_KEY, self::$hmacKey);
            }
        } else {
            //查看是否开启openssl模块
            if (!extension_loaded('openssl')) {
                throw new \Exception('openssl extension not installed');
            }
            if (file_exists(FILE::SECRET_DIR . "/" . FILE::OPENSSL_PRIVATE_KEY)) {
                self::$privateKey =  file_get_contents(FILE::SECRET_DIR . "/" . FILE::OPENSSL_PRIVATE_KEY);
                self::$publicKey =  file_get_contents(FILE::SECRET_DIR . "/" . FILE::OPENSSL_PUBLIC_KEY);
            } else {
                $this->generateOpensslKey();
            }
        }
    }

    /**
     * 生成openssl文件
     * @return void
     */
    private function generateOpensslKey() : void
    {
        $config = array(
            "digest_alg" => strtolower($this->type),
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $resource = openssl_pkey_new($config);
        //生成秘钥
        openssl_pkey_export($resource, $privateKey);
        File::writeFile(FILE::SECRET_DIR, FILE::OPENSSL_PRIVATE_KEY, $privateKey);
        //生成公钥
        $publicKeyArray = openssl_pkey_get_details($resource);
        File::writeFile(FILE::SECRET_DIR, FILE::OPENSSL_PUBLIC_KEY, $publicKeyArray["key"]);
        self::$privateKey = $privateKey;
        self::$publicKey = $publicKeyArray;
    }
}