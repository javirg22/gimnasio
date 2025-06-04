<?php
require __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


class Auth {
    private static $secret_key = "tu_clave_secreta"; // Cambia esto por una clave segura
    private static $algorithm = "HS256";
    private static $expiration_time = 3600; // 1 hora

    // Generar un JWT
    public static function generarToken($user_id, $email) {
        $payload = [
            "iat" => time(), // Emitido en
            "exp" => time() + self::$expiration_time, // Expira en 1 hora
            "user_id" => $user_id,
            "email" => $email
        ];
        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    // Verificar un JWT
    public static function verificarToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$algorithm));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
?>
