<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/jwt.php';

header('Content-Type: application/json');
$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Token requerido"]);
    exit;
}

$user_id = verifyJWT($headers['Authorization']);
if (!$user_id) {
    echo json_encode(["error" => "Token inválido"]);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Insertar o actualizar membresía activa
    $stmt = $pdo->prepare("REPLACE INTO membresias (user_id, fecha_inicio, fecha_fin) VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))");
    $stmt->execute([$user_id]);

    echo json_encode(["message" => "Membresía activada o renovada"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al activar membresía: " . $e->getMessage()]);
}
