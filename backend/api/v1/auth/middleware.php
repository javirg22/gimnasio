<?php
require_once __DIR__ . '/../../auth.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

function verificarAutenticacion() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        echo json_encode(["error" => "Token no proporcionado"]);
        http_response_code(401);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);
    $data = Auth::verificarToken($token);

    if (!$data) {
        echo json_encode(["error" => "Token inválido o expirado"]);
        http_response_code(401);
        exit;
    }

    return $data;
}
?>
