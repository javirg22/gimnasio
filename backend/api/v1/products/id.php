<?php
// Se incluyen los archivos necesarios para la conexión a la base de datos y el modelo de productos
require_once '../../../config/database.php';
require_once '../../../models/Product.php';

// Configuración de las cabeceras para la API
header('Content-Type: application/json'); // Especifica que la respuesta será en formato JSON
header("Access-Control-Allow-Origin: *"); // Permite el acceso desde cualquier origen (CORS habilitado)
header("Access-Control-Allow-Methods: GET, PUT, DELETE"); // Permite los métodos GET, PUT y DELETE
header("Access-Control-Allow-Headers: Content-Type"); // Permite el envío de datos en formato JSON

// Se instancia la conexión a la base de datos y la clase Product
$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Se obtiene el ID del producto desde la URL (parámetro GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Convierte el ID a un número entero
if ($id <= 0) {
    echo json_encode(["error" => "ID de producto no válido"]);
    http_response_code(400);
    exit;
}

// Se obtiene el método HTTP de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Si el método es GET, se intenta obtener la información del producto
    if ($product->getAllProducts($id)) {
        $response = [
            "id" => $product->$id,
            "name" => $product->$name,
            "description" => $product->$description,
            "price" => $product->$price,
            "image_url" => $product->$image_url
        ];
        echo json_encode($response);
        http_response_code(200);
    } else {
        echo json_encode(["error" => "Producto no encontrado"]);
        http_response_code(404);
    }
} elseif ($method === 'PUT') {
    // Si el método es PUT, se espera actualizar un producto
    $data = json_decode(file_get_contents("php://input"));

    // Verifica si se recibieron los datos necesarios
    if (!isset($data->name) || !isset($data->price) || !isset($data->description) || !isset($data->stock) || !isset($data->image_url)) {
        echo json_encode(["error" => "Faltan datos obligatorios"]);
        http_response_code(400);
        exit;
    }

    // Asignación de propiedades
    $name = $data->name;
    $price = $data->price;
    $description = $data->description;
    $stock = $data->stock;
    $image_url = $data->image_url;

    // Se intenta actualizar el producto
    if ($product->createProduct($id, $name, $description, $price, $stock, $image_url)) {
        echo json_encode(["message" => "Producto actualizado con éxito"]);
        http_response_code(200);
    } else {
        echo json_encode(["error" => "Error al actualizar el producto"]);
        http_response_code(500);
    }
}


