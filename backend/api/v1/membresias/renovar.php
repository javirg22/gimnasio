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

    // Extiende la fecha_fin un mes más desde la actual
    $stmt = $pdo->prepare("UPDATE membresias SET fecha_fin = DATE_ADD(fecha_fin, INTERVAL 1 MONTH) WHERE user_id = ?");
    $stmt->execute([$user_id]);

    echo json_encode(["message" => "Membresía renovada"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al renovar membresía: " . $e->getMessage()]);
}
