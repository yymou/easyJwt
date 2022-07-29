<?php

require_once "vendor/autoload.php";

$access = new \EasyJwt\Jwt();
$token = $access->setAlgorithm("HS256");
$token = $access->getToken(['info' => 'value']);
//echo $token;
$res = $access->verifyToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NTkwOTE5MzAsImV4cCI6MTY1OTE3ODMzMCwic3ViIjoiKiIsImp0aSI6IiIsIm5iZiI6MTY1OTA5MTkzMCwiaW5mbyI6InZhbHVlIn0.R_CHQJyIQ2YJ20pQ2pUOSFNv_DD2iGIMsMRYZ78rw0PiF47aQusFLIrs5cuYNfAgo97fSjvRY7kKrYihI6SI4o7TQ7nZhnD0pyxxgMtnw9eTx2AwjiQgIoAb10Uivqe_IYz4BmdU5ZYQL9N4xCrGJEWgMa9BjruzJDXJAvOKs943FiZYdUv7LFo4Twip5zkJDNZmYarKrDdJwda9slNDArz1QRsmTIDUlJiAwnERU5toj4iLGJiihGApNmebuqOI1PoPUsamvdaJh32dENF7ni82wgP9mUsvI9xEcpBH7n-eGsC9pj0Zo_H30trYg7VwsiOGzk6XghZfO68GkecXoA");
echo "</pre>";
var_dump($res); exit;
