<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/jwt.php';

header('Content-Type: application/json');
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

$user_id = $datosUsuario['userId'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Eliminar membresía
    $stmt = $pdo->prepare("DELETE FROM membresias WHERE id_usuario = ?");
    $stmt->execute([$user_id]);

    echo json_encode(["message" => "Membresía cancelada"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al cancelar membresía: " . $e->getMessage()]);
}
