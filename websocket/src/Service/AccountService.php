<?php

namespace Hydrignis\Websocket\Service;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AccountService {
    public static function decodeToken(string $token): false|array {
        try {
            $decoded = JWT::decode($token, new Key($_ENV["TOKENS_KEY"], 'HS256'));
            return (array)$decoded;

        } catch (\Exception $e) {
            echo "Token: ".$e->getMessage();
            return false;
        }
    }
}