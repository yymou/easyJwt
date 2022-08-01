<?php
declare(strict_types=1);

use EasyJwt\Jwt;
use PHPUnit\Framework\TestCase;

class JwtTest extends TestCase
{
    public function testGetToken() : string
    {
        $info = ['a' => 'b'];
        $token = (new Jwt())->getToken($info);
        $this->assertNotEmpty($token);
        return $token;
    }

    /**
     * @depends testGetToken
     * @return void
     */
    public function testVerifyToken(string $token) : void
    {
        $this->assertArrayHasKey(['a' => 'b'], (new Jwt())->verifyToken($token));
    }


}
