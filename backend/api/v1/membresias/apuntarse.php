<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/jwt.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Token requerido"]);
    exit;
}

$datosUsuario = verifyJWT($headers['Authorization']);
if (!$datosUsuario || !isset($datosUsuario['userId'])) {
    echo json_encode(["error" => "Token inválido o ID no encontrado"]);
    exit;
}

$user_id = $datosUsuario['userId']; // Aquí coges el ID correctamente

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Insertar o actualizar membresía activa
    $stmt = $pdo->prepare("REPLACE INTO membresias (id_usuario, fecha_inicio, fecha_fin) VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))");
    $stmt->execute([$user_id]);

    echo json_encode(["message" => "Membresía activada o renovada"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al activar membresía: " . $e->getMessage()]);
}

