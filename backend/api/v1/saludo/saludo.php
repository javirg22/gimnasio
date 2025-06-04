<?php
require_once '../../../config/database.php';
require_once '../../../models/User.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID de usuario requerido"]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$id = intval($_GET['id']);
$usuario = $user->getUserById($id);

if ($usuario) {
    echo json_encode($usuario);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Usuario no encontrado"]);
}
