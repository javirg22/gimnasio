<?php
require_once '../../../config/database.php';
require_once __DIR__ . '/../../../models/Order.php';
require_once __DIR__ . '/../../../config/jwt.php';


header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(["error" => "ID de pedido no válido"]);
    http_response_code(400);
    exit;
}

$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Token no proporcionado"]);
    http_response_code(401);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);
$data = verifyJWT($token);
if (!$decoded) {
    echo json_encode(["error" => "Token inválido"]);
    http_response_code(401);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if ($order->getOrderById($id, $decoded->id)) {
        echo json_encode($order);
        http_response_code(200);
    } else {
        echo json_encode(["error" => "Pedido no encontrado"]);
        http_response_code(404);
    }
} elseif ($method === 'DELETE') {
    if ($order->deleteOrder($id, $decoded->id)) {
        echo json_encode(["message" => "Pedido eliminado con éxito"]);
        http_response_code(200);
    } else {
        echo json_encode(["error" => "Error al eliminar el pedido"]);
        http_response_code(500);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
    http_response_code(405);
}
