<?php
require __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key; // Necesario para decodificación en versiones nuevas
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Clave secreta para la firma del JWT (manténla segura)
$key = "tu_clave_secreta";

// Función para generar un token JWT
function generateJWT($userId) {
    global $key;
    
    // Definir la carga útil del token
    $payload = [
        "iss" => "techshop",  // Emisor
        "iat" => time(),      // Fecha de emisión
        "exp" => time() + 3600,  // Expiración en 1 hora
        "userId" => $userId   // ID del usuario
    ];

    // ✅ CORREGIDO: Se agregan los 3 parámetros necesarios
    return JWT::encode($payload, $key, 'HS256');
}

// Función para verificar y decodificar un JWT
function verifyJWT($jwt) {
    global $key;
    
    try {
        // ✅ CORREGIDO: Se usa `new Key($key, 'HS256')` para la verificación en versiones nuevas
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        return (array) $decoded; // Retornar la carga útil del token como un array
    } catch (Exception $e) {
        return null; // Token inválido o expirado
    }
}
?>

