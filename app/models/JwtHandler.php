<?php
// JwtHandler.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler {
   public static function generate_jwt_token($user_id, $secret_key) {
       $issued_at = time();
       $expiration_time = $issued_at + (60 * 60); // valid for 1 hour

       $payload = array(
           'iat' => $issued_at,
           'exp' => $expiration_time,
           'sub' => $user_id,
       );
   
       return JWT::encode($payload, $secret_key, 'HS256');
   }
   
   public static function validate_jwt_token($jwt_token, $secret_key) {
       try {
           return JWT::decode($jwt_token, new Key($secret_key, 'HS256'));
       } catch (Exception $e) {
           throw new Exception($e);
       }
   }
}