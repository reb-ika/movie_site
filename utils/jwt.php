<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTException extends Exception {}

function generateJWT($payload) {
    $secretKey = 'your-secret-key-here'; // Change this to a secure key
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // 1 hour expiration

    $payload += [
        'iat' => $issuedAt,
        'exp' => $expirationTime
    ];

    return JWT::encode($payload, $secretKey, 'HS256');
}

function validateJWT($token) {
    $secretKey = 'your-secret-key-here';
    
    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        return (array)$decoded;
    } catch (Exception $e) {
        throw new JWTException('Invalid token: ' . $e->getMessage(), 401);
    }
}
?>