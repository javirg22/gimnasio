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

    // Extiende la fecha_fin un mes más desde la actual
    $stmt = $pdo->prepare("UPDATE membresias SET fecha_fin = DATE_ADD(fecha_fin, INTERVAL 1 MONTH) WHERE id_usuario = ?");
    $stmt->execute([$user_id]);

    echo json_encode(["message" => "Membresía renovada"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al renovar membresía: " . $e->getMessage()]);
}

