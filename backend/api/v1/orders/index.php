<?php
require_once '../../../config/database.php';
require_once '../../../models/Order.php';
require_once '../../../config/jwt.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// ✅ Obtener el ID del usuario correctamente desde GET o desde el token JWT
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if (!$user_id) {
    echo json_encode(["error" => "ID de usuario requerido"]);
    exit;
}

try {
    $pdo = new PDO($dsn, $db_user, $db_password, $options);
    $order = new Order($pdo);

    // ✅ Usar $user_id correctamente sin $this->
    $orders = $order->getOrdersByUser($user_id);

    echo json_encode(["orders" => $orders]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al obtener los pedidos"]);
}
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        echo json_encode(["error" => "Token no proporcionado"]);
        http_response_code(401);
        exit;
    }
    
    $token = str_replace("Bearer ", "", $headers['Authorization']);
    $decoded = verifyJWT($token);
    if (!$decoded) {
        echo json_encode(["error" => "Token inválido"]);
        http_response_code(401);
        exit;
    }
    
    $stmt = $order->getOrdersByUser($user_id);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($orders);
    http_response_code(200);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->user_id) || !isset($data->items) || !is_array($data->items)) {
        echo json_encode(["error" => "Datos incompletos"]);
        http_response_code(400);
        exit;
    }
    
    $order->$user_id = $data->user_id;
    $order->$items = json_encode($data->items);
    
    if ($order->createOrder($user_id, $total_price, $status)) {
        echo json_encode(["message" => "Pedido creado con éxito"]);
        http_response_code(201);
    } else {
        echo json_encode(["error" => "Error al crear el pedido"]);
        http_response_code(500);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
    http_response_code(405);
}
