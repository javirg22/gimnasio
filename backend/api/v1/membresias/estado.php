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

$user_id = $datosUsuario['userId'];

// Aquí defines los precios (pueden ser variables o constantes)
$precio_membresia = 20; // ejemplo 20€
$precio_renovacion = 15; // ejemplo 15€

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Consultar si hay membresía activa
    $stmt = $pdo->prepare("SELECT fecha_fin FROM membresias WHERE id_usuario = ? AND fecha_fin > NOW()");
    $stmt->execute([$user_id]);
    $membresia = $stmt->fetch(PDO::FETCH_ASSOC);

    $tiene_membresia = $membresia ? true : false;

    echo json_encode([
        "tiene_membresia" => $tiene_membresia,
        "fecha_fin" => $membresia['fecha_fin'] ?? null,
        "precio_membresia" => $precio_membresia,
        "precio_renovacion" => $precio_renovacion
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al obtener estado de membresía: " . $e->getMessage()]);
}
