<?php
require_once __DIR__ . '/../../auth.php';

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
