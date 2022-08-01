<?php

namespace EasyJwt\Util;

/**
 * 文件操作
 * @author yangyanlei
 * @email 875167485@qq.com
 * @date 2022/7/29
 */
class File
{
    const SECRET_DIR = EASYJWT_ROOT . "/secret";
    const OPENSSL_PRIVATE_KEY = "openssl-private.key";
    const OPENSSL_PUBLIC_KEY = "openssl-public.key";
    const HMAC_KEY = 'hmac.key';
    /**
     * 写入内容到文件
     * @param string $file
     * @param string $content
     * @return void
     */
    public static function writeFile(string $dir, string $file, string $content)
    {
        if (!chmod($dir, 0775)) {
            throw new \Exception('请赋予文件夹它该有的权限,谢谢');
        }
        $hander = fopen($dir . '/' . $file, 'w');
        if (empty($hander)) {
            throw new \Exception('文件打不开呀');
        }
        fwrite($hander, $content);
        fclose($hander);
    }
}