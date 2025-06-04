<?php
require_once '../../../config/database.php';
require_once '../../../models/clase.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");


$database = new Database();
$db = $database->getConnection();
$clase = new Clase($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $clase->getAllClases();
    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($clases)) {
        http_response_code(404);
        echo json_encode(["error" => "No hay clases disponibles"]);
    } else {
        http_response_code(200);
        echo json_encode($clases);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}

