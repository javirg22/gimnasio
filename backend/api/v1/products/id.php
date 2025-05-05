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
    echo json_encode(["error" => "ID de producto no válido"]); // Devuelve error si el ID no es válido
    http_response_code(400); // Código de error 400 (Bad Request)
    exit; // Termina la ejecución del script
}

// Se obtiene el método HTTP de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Si el método es GET, se intenta obtener la información del producto
    if ($product->getAllProducts($id)) {
        echo json_encode($product); // Devuelve el producto en formato JSON
        http_response_code(200); // Código de éxito 200 (OK)
    } else {
        echo json_encode(["error" => "Producto no encontrado"]); // Devuelve error si no se encuentra el producto
        http_response_code(404); // Código de error 404 (Not Found)
    }
} elseif ($method === 'PUT') {
    // Si el método es PUT, se espera actualizar un producto

    // Se obtiene el cuerpo de la solicitud en formato JSON
    $data = json_decode(file_get_contents("php://input"));

    // Verifica si se recibieron los datos necesarios
    if (!isset($data->name) || !isset($data->price) || !isset($data->description)) {
        echo json_encode(["error" => "Faltan datos obligatorios"]); // Devuelve error si faltan datos
        http_response_code(400); // Código de error 400 (Bad Request)
        exit;
    }

    // Se asignan los valores del producto desde la solicitud
    $product->$name = $data->name;
    $product->$price = $data->price;
    $product->$description = $data->description;

    // Se intenta actualizar el producto en la base de datos
    if ($product->createProduct($id, $name, $description, $price, $stock)) {
        echo json_encode(["message" => "Producto actualizado con éxito"]); // Devuelve mensaje de éxito
        http_response_code(200); // Código de éxito 200 (OK)
    } else {
        echo json_encode(["error" => "Error al actualizar el producto"]); // Devuelve error si no se pudo actualizar
        http_response_code(500); // Código de error 500 (Internal Server Error)
    }
}

