<?php
require_once __DIR__ . '/../../../config/database.php'; // Conexión a la BD
require_once __DIR__ . '/../../../config/jwt.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Verificar que se reciban todos los datos necesarios
if (!isset($data['nombre'], $data['email'], $data['password'])) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$nombre = htmlspecialchars($data['nombre']);
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$password = password_hash($data['password'], PASSWORD_BCRYPT);

// Verificar que el correo electrónico sea válido
if (!$email) {
    echo json_encode(["error" => "Correo electrónico no válido"]);
    exit;
}

try {
    // Usar la clase Database para obtener la conexión
    $database = new Database();
    $pdo = $database->getConnection();  // Conexión obtenida de la clase Database

    // Verificar si el email ya está registrado en la tabla 'usuarios'
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo json_encode(["error" => "El correo ya está registrado"]);
        exit;
    }

    // Insertar el nuevo usuario en la tabla 'usuarios'
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $email, $password]);

    // Obtener el ID del usuario recién registrado
    $user_id = $pdo->lastInsertId();

    // Generar el token JWT
    $token = generateJWT($user_id);

    echo json_encode(["message" => "Usuario registrado con éxito", "token" => $token]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en el registro: " . $e->getMessage()]);
}
?>


