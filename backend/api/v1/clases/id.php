<?php
require_once '../../../config/database.php';
require_once '../../../models/clase.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$database = new Database();
$db = $database->getConnection();
$clase = new Clase($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($method === 'GET') {
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "ID no válido"]);
        exit;
    }

    if ($clase->getClaseById($id)) {
        http_response_code(200);
        echo json_encode($clase);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Clase no encontrada"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>
