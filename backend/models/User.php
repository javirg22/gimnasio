<?php
// User.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "usuarios";
    private $table_membresias = "membresias";

    public function __construct($db) {
        $this->conn = $db;
        error_log("User::__construct - Conexión DB establecida");
    }

    // Registro de usuario
    public function register($name, $email, $password) {
        error_log("User::register - Registro: $name, $email");
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, email, contrasena) 
                  VALUES (:name, :email, :password)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        
        $result = $stmt->execute();
        error_log("User::register - Resultado execute: " . ($result ? "OK" : "FAIL"));
        return $result;
    }

    // Inicio de sesión
    public function login($email, $password) {
        error_log("User::login - Intento login: $email");
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            error_log("User::login - Usuario encontrado, verificando contraseña");
            if (password_verify($password, $user['contrasena'])) {
                error_log("User::login - Contraseña correcta");
                return $user;
            } else {
                error_log("User::login - Contraseña incorrecta");
            }
        } else {
            error_log("User::login - Usuario no encontrado");
        }
        return false;
    }

    // Obtener usuario por ID
    public function getUserById($id) {
        error_log("User::getUserById - Buscando usuario con ID: $id");
        $query = "SELECT id_usuario AS id, nombre AS name, email 
                  FROM " . $this->table_name . " 
                  WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("User::getUserById - Resultado: " . print_r($result, true));
        return $result;
    }

    // Comprueba si un usuario tiene una membresía activa (fecha_fin > NOW())
    public function tieneMembresiaActiva($userId) {
        error_log("User::tieneMembresiaActiva - Verificando membresía para usuario $userId");
        $query = "SELECT COUNT(*) AS total FROM " . $this->table_membresias . " 
                  WHERE id_usuario = :userId AND fecha_fin > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hasMembership = $row && $row['total'] > 0;
        error_log("User::tieneMembresiaActiva - Tiene membresía activa: " . ($hasMembership ? "Sí" : "No"));
        return $hasMembership;
    }
    
}

