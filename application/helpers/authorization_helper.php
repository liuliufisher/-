<?php

require_once APPPATH . 'libraries/JWT.php';

use \Firebase\JWT\JWT;

class Authorization {

    public static function validateToken($token) {
        return JWT::decode($token, JWT_KEY, array(JWT_ALGORITHM));
    }

    public static function generateToken($data) {
        return JWT::encode($data, JWT_KEY);
    }
    
}



