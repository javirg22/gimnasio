<?php
require_once '../../../config/database.php';
require_once '../../../models/Product.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
ini_set('display_errors', 1);
error_reporting(E_ALL);

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        // Obtiene todos los productos
        $stmt = $product->getAllProducts();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Depuración para verificar el contenido de los productos
        if (empty($products)) {
            echo json_encode(["error" => "No hay productos disponibles"]);
            http_response_code(404);
        } else {
            // Mostrar los productos obtenidos directamente como un array
            echo json_encode($products);
            http_response_code(200);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Error al obtener los productos: " . $e->getMessage()]);
        http_response_code(500);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
    http_response_code(405);
}
