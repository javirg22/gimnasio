<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/jwt.php';

header('Content-Type: application/json');
$headers = getallheaders();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($headers['Authorization'], $data['clase_id'])) {
    echo json_encode(["error" => "Token y clase_id son requeridos"]);
    exit;
}

$user_id = verifyJWT($headers['Authorization']);
if (!$user_id) {
    echo json_encode(["error" => "Token inválido"]);
    exit;
}

$clase_id = $data['clase_id'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Verifica si hay cupos
    $stmt = $pdo->prepare("SELECT cupos FROM clases WHERE id = ?");
    $stmt->execute([$clase_id]);
    $clase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clase || $clase['cupos'] <= 0) {
        echo json_encode(["error" => "Clase sin cupos disponibles"]);
        exit;
    }

    // Insertar reserva
    $stmt = $pdo->prepare("INSERT INTO reservas (user_id, clase_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $clase_id]);

    // Reducir cupo
    $stmt = $pdo->prepare("UPDATE clases SET cupos = cupos - 1 WHERE id = ?");
    $stmt->execute([$clase_id]);

    echo json_encode(["message" => "Clase reservada con éxito"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al reservar clase: " . $e->getMessage()]);
}
