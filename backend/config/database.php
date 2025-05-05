<?php
// Configuración de la base de datos
$host = '127.0.0.1'; // O la IP de tu servidor MySQL
$dbname = 'gymrat'; // Nombre de la base de datos
$db_user = 'root'; // Nombre de usuario
$db_password = ''; // Contraseña
$charset = 'utf8mb4';

// Clase Database
class Database {
    private $host = '127.0.0.1';
    private $dbname = 'gymrat';
    private $db_user = 'root';
    private $db_password = '';
    private $charset = 'utf8mb4';
    private $pdo;

    // Método para obtener la conexión
    public function getConnection() {
        if ($this->pdo === null) {
            try {
                // El DSN completo para MySQL
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

                // Opciones de conexión
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                // Establecer la conexión
                $this->pdo = new PDO($dsn, $this->db_user, $this->db_password, $options);
            } catch (PDOException $e) {
                echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
                exit;
            }
        }
        return $this->pdo;
    }
}

?>

