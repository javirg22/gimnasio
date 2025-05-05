<?php
require_once '../config/database.php';
require_once '../utils/helpers.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$apiIndex = array_search('api', $requestUri);
if ($apiIndex === false || !isset($requestUri[$apiIndex + 1])) {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint no encontrado"]);
    exit;
}

$version = $requestUri[$apiIndex + 1];
$endpoint = isset($requestUri[$apiIndex + 2]) ? $requestUri[$apiIndex + 2] : '';
$resourceId = isset($requestUri[$apiIndex + 3]) ? $requestUri[$apiIndex + 3] : null;

$method = $_SERVER['REQUEST_METHOD'];
$basePath = __DIR__ . "/v1/";

$routes = [
    'auth' => ['path' => 'auth/', 'methods' => ['POST']],
    'products' => ['path' => 'products/', 'methods' => ['GET', 'POST']],
    'orders' => ['path' => 'orders/', 'methods' => ['GET', 'POST']],
    'payments' => ['path' => 'payments/', 'methods' => ['POST']]
];

if (isset($routes[$endpoint])) {
    $route = $routes[$endpoint];
    if (!in_array($method, $route['methods'])) {
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        exit;
    }
    
    $file = $basePath . $route['path'] . ($resourceId ? 'id.php' : 'index.php');
    if (file_exists($file)) {
        require $file;
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Archivo no encontrado"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => "Ruta no válida"]);
}
