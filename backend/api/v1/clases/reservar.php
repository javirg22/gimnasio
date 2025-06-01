<?php
error_log("reservar.php llamado");

require_once '../../../config/database.php';
require_once '../../../models/reserva.php';
require_once '../../../models/clase.php';
require_once '../../../models/User.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$database = new Database();
$db = $database->getConnection();

$reserva = new Reserva($db);
$clase = new Clase($db);
$usuario = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!isset($_GET['id_usuario'])) {
        error_log("GET: id_usuario no proporcionado");
        http_response_code(400);
        echo json_encode(["error" => "Se requiere id_usuario"]);
        exit;
    }

    $userId = intval($_GET['id_usuario']);
    $reservas = $reserva->getReservasByUser($userId);
    http_response_code(200);
    echo json_encode($reservas);
    exit;
}

if ($method === 'POST') {
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("ERROR JSON: " . json_last_error_msg());
        http_response_code(400);
        echo json_encode(["error" => "JSON inválido: " . json_last_error_msg()]);
        exit;
    }

    if (!isset($data->id_usuario) || !isset($data->id_clase)) {
        error_log("Faltan datos: id_usuario y/o id_clase");
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos: id_usuario y id_clase requeridos"]);
        exit;
    }

    $userId = intval($data->id_usuario);
    $claseId = intval($data->id_clase);

    // Verificar membresía activa del usuario
    if (!$usuario->tieneMembresiaActiva($userId)) {
        http_response_code(403);
        echo json_encode(["error" => "No tienes membresía activa para reservar clases"]);
        exit;
    }

    // Verificar que la clase existe y tiene aforo
    if (!$clase->getClaseById($claseId)) {
        http_response_code(404);
        echo json_encode(["error" => "Clase no encontrada"]);
        exit;
    }

    if ($clase->aforo <= 0) {
        http_response_code(409);
        echo json_encode(["error" => "La clase está completa"]);
        exit;
    }

    // Verificar reserva previa en últimas 24 horas
    if ($reserva->reservadoEnUltimas24h($userId, $claseId)) {
        http_response_code(409);
        echo json_encode(["error" => "No puedes reservar la misma clase en menos de 24 horas"]);
        exit;
    }

    // Crear reserva
    if ($reserva->crearReserva($userId, $claseId)) {
        $clase->reducirAforo($claseId);
        http_response_code(201);
        echo json_encode(["message" => "Reserva realizada con éxito"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al crear la reserva"]);
    }
} elseif ($method === 'DELETE') {
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("ERROR JSON en DELETE: " . json_last_error_msg());
        http_response_code(400);
        echo json_encode(["error" => "JSON inválido: " . json_last_error_msg()]);
        exit;
    }

    if (!isset($data->id_usuario) || !isset($data->id_clase)) {
        error_log("DELETE: Faltan datos");
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos: id_usuario y id_clase requeridos para eliminar la reserva"]);
        exit;
    }

    $userId = intval($data->id_usuario);
    $claseId = intval($data->id_clase);

    if ($reserva->eliminarReserva($userId, $claseId)) {
        $clase->incrementarAforo($claseId);
        http_response_code(200);
        echo json_encode(["message" => "Reserva eliminada con éxito"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar la reserva"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
