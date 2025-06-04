<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/jwt.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "Token requerido"]);
    exit;
}

$datosUsuario = verifyJWT($headers['Authorization']);
if (!$datosUsuario || !isset($datosUsuario['userId'])) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido o ID no encontrado"]);
    exit;
}

$user_id = $datosUsuario['userId'];

// Recibimos JSON con la duración o tipo de membresía
$data = json_decode(file_get_contents("php://input"));
if (!isset($data->diasDuracion)) {
    http_response_code(400);
    echo json_encode(["error" => "Duración no especificada"]);
    exit;
}

$diasDuracion = intval($data->diasDuracion);

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Comprobar si ya existe membresía activa
    $stmt = $pdo->prepare("SELECT fecha_fin FROM membresias WHERE id_usuario = ? AND fecha_fin > NOW()");
    $stmt->execute([$user_id]);
    $membresia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($membresia) {
        // Si ya hay membresía activa, extendemos la fecha_fin sumando días
        $fechaFinActual = new DateTime($membresia['fecha_fin']);
        $fechaFinActual->modify("+$diasDuracion days");
        $nuevaFechaFin = $fechaFinActual->format('Y-m-d H:i:s');

        $update = $pdo->prepare("UPDATE membresias SET fecha_fin = ? WHERE id_usuario = ? AND fecha_fin > NOW()");
        $update->execute([$nuevaFechaFin, $user_id]);
    } else {
        // Si no hay membresía activa, insertamos nueva desde hoy
        $fechaInicio = new DateTime();
        $fechaFin = $fechaInicio->modify("+$diasDuracion days")->format('Y-m-d H:i:s');

        $insert = $pdo->prepare("INSERT INTO membresias (id_usuario, fecha_inicio, fecha_fin) VALUES (?, NOW(), ?)");
        $insert->execute([$user_id, $fechaFin]);
    }

    echo json_encode(["mensaje" => "Membresía actualizada correctamente"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar membresía: " . $e->getMessage()]);
}
