<?php

namespace EasyJwt\Component;

use EasyJwt\Config;

/**
 * payload对象
 * @author yangyanlei
 * @email 875167485@qq.com
 * @date 2022/7/29
 */
class Payload
{
    //签发时间
    public $iat = EASYJWT_NOW_TIME;

    //过期时间
    public $exp = EASYJWT_NOW_TIME + Config::EXPIRE_TIME;

    //面向的用户
    public $sub = Config::SUB_USER;

    //该token的唯一标识
    public $jti = '';

    //该时间之前不接受处理该token
    public $nbf = EASYJWT_NOW_TIME;
}