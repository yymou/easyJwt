<?php

namespace EasyJwt\Util;

/**
 * 单例类
 */
trait Single
{
    private static $instance;

    //获取实例
    static function getInstance(...$args) : object
    {
        if (!isset(self::$instance)) {
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }

    //私有化克隆方法
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}
?>