<?php
declare(strict_types=1);

use EasyJwt\Jwt;
use PHPUnit\Framework\TestCase;

class JwtTest extends TestCase
{
    public function testGetToken() : string
    {
        $token = (new Jwt())->setPayload(['a' => 'b'])->setExp(2)->getToken();
        $this->assertNotEmpty($token);
        return $token;
    }

    /**
     * @depends testGetToken
     * @return void
     */
    public function testVerifyToken(string $token) : void
    {
        $this->assertArrayHasKey('a', (new Jwt())->explainToken($token)->getPayload());
    }


}
