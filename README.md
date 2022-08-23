## EasyJwt

##如果你想要快速上手并将jwt运用到项目中,就来试试EasyJwt吧!
>它尽可能的简化使用jwt的一些前期工作,composer拉过来两三行代码即用, 你甚至不用去管理秘钥...

此处阅读jwt的相关信息:

* [RFC 7519](https://tools.ietf.org/html/rfc7519)
* [jwt.io](https://jwt.io/introduction/)
* [JWT Handbook](https://auth0.com/resources/ebooks/jwt-handbook)

## 安装

```composer
composer require yymou/easyjwt
```
完毕后引入到项目
```php
require 'vendor/autoload.php';
```
之后就可以愉快的使用啦

## 示例

+ 生成token
> 需要提供 payload 数据, 项目中一般情况这个值存储登录用户数据 如:$payload = ["uid" => 123];
```php
    $token = (new EasyJwt\Jwt())->setPayload($payload)->getToken();
```
+ 验证token
```php
    (new EasyJwt\Jwt())->explainToken($token)->getPayload();
```

## 搞定

---

### 如果你想个性化一下参数的话 可以往下看...
> 以下 EasyJwt\Jwt() 均写为 $jwtObj;

1. 你可以定义加密的算法
   1. 支持的算法如下:
        * HS256
        * HS384
        * HS512
        * RS256
        * RS384
        * RS512
   2. 示例 (两种方式, 以"HS256"为例)
      1. 
       ```php
          $token = $jwtObj->setAlgorithm("HS256")->setPayload($payload)->getToken();
       ```
      2.
      ```php
          $token = (new EasyJwt\Jwt("HS256"))->setPayload($payload)->getToken();
      ```
2. 你可以自定义加密秘钥
   1. 根据加密算法不同 需要秘钥的类型也不同 openssl需要私钥公钥, hmac只需要一个秘钥即可
   2. 示例
      ```php
         $token = $jwtObj->setKey("你的私钥")->setPayload($payload)->getToken();
      ```
   > 如果不定义自己的秘钥, easyJwt会帮你自动生成秘钥文件, 路径存放在: {你的项目路径}/vendor/yymou/easyjwt/src/secret/ 下, 结构如下:
   
   + vendor
     + yymou
       + easyJwt
         + secret
           + hmac.key -- hmac算法使用秘钥
           + openssl-private.key -- openssl算法使用私钥
           + openssl-public.key -- openssl算法使用公钥
           
   > 需要的话可以自行查阅, 注意各环境的秘钥不要污染!
3. 你可以自定义token过期时间
   1. 默认的过期时间是86400s
   2. 示例
   ```php
      $token = $jwtObj->setExp(86400*7)->setPayload($payload)->getToken();
   ```

## 解析token
   ```php
    $jwtObj->explainToken($token)->getPayload();
   ```
   + explainToken()后支持的方法
      + getPayload() -- 获取payload实体
      + getKey() -- 获取当前加密秘钥
      + getHeader() -- 获取header

## 实例代码 (仅供参考) php 7.2+
```php
<?php

namespace App\Business\Service;

use EasyJwt\Jwt;

/**
 * 通行相关逻辑
 */
class AccessService extends BaseService
{
    public $errorMsg = '';

    private $jwtModel;

    const JWT_EXPIRE_TIME = 86400 * 15;

    function __construct()
    {
        parent::__construct();
        $this->jwtModel = new Jwt();
    }

    /**
     * 验证bearer token
     * @return bool|array
     */
    public function verifyBearerToken()
    {
        $token = $this->getBearerToken();
        if (empty($token)) {
            return false;
        }

        return $this->jwtModel->explainToken($token)->getPayload();
    }

    /**
     * Get header Authorization
     * */
    public function getAuthorizationHeader()
    {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    //刷新token
    public function refreshToken(array $payload) : string
    {
        if (!empty($payload)) {
            return $this->jwtModel->setExp(self::JWT_EXPIRE_TIME)->setPayload($payload)->getToken();
        }
        return '';
    }
}

```