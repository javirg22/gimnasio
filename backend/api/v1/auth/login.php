<?php
require_once __DIR__ . '/../../../config/database.php'; // Conexión a la BD
require_once __DIR__ . '/../../../config/jwt.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Obtener los datos enviados en el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Verificar que se recibieron los datos necesarios
if (!isset($data['email'], $data['password'])) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$password = $data['password'];

if (!$email) {
    echo json_encode(["error" => "Correo electrónico no válido"]);
    exit;
}

try {
    // Usar la clase Database para obtener la conexión
    $database = new Database();
    $pdo = $database->getConnection();  // Conexión obtenida de la clase Database

    // Verificar si el email existe en la base de datos
    $stmt = $pdo->prepare("SELECT id_usuario, contrasena FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['contrasena'])) {
        // Generar el token JWT si las credenciales son correctas
        $token = generateJWT($user['id_usuario']);
        
        // Responder con el token y el ID del usuario
        echo json_encode([
            "message" => "Inicio de sesión exitoso",
            "token" => $token,
            "user_id" => $user['id_usuario']
        ]);
    } else {
        echo json_encode(["error" => "Correo o contraseña incorrectos"]);
    }

    exit; // 🔥 Evita respuestas adicionales
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en el login: " . $e->getMessage()]);
    exit;
}
?>


