<?php

namespace EasyJwt\Component;

use EasyJwt\Util\File;
use Nowakowskir\JWT\JWT;
use phpDocumentor\Reflection\Types\Void_;

/**
 * 加密秘钥
 * @author yangyanlei
 * @email 875167485@qq.com
 * @date 2022/7/29
 */
class EncryptionKey
{
    //定义加密方法
    private $func;

    //定义加密类型
    private $type;

    private static $key;

    public function __construct(string $algorithm)
    {
        list($this->func, $this->type) = JWT::ALGORITHMS[$algorithm];
        $this->checkKey();
    }

    //获取key
    public static function get()
    {
        return self::$key ?? '';
    }

    /**
     * 生成秘钥
     * @return string
     */
    private function checkKey() : void
    {
        $key = '';
        switch ($this->func) {
            case 'hash_hmac' :

                break;
            case 'openssl' :
                //查看是否开启openssl模块
                if (!extension_loaded('openssl')) {
                    throw new \Exception('亲 请安装php的openssl扩展哦');
                }
                if (file_exists(FILE::SECRET_DIR . "/" . FILE::OPENSSL_PRIVATE_KEY)) {
                    self::$key =  file_get_contents(FILE::SECRET_DIR . "/" . FILE::OPENSSL_PRIVATE_KEY);
                } else {
                    $this->generateOpensslKey();
                }
                break;
            default:
                throw new \Exception('Unsupported algorithm type');
                break;
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
        self::$key = $privateKey;
    }
}