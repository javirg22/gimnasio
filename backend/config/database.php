<?php 
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configuración de la base de datos
//$host = '127.0.0.1'; // O la IP de tu servidor MySQL local
//$dbname = 'gymrat'; // Nombre de la base de datos local
//$db_user = 'root'; // Nombre de usuario local
//$db_password = ''; // Contraseña local
//$charset = 'utf8mb4';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Clase Database
class Database {
    private $host;
    private $dbname;
    private $db_user;
    private $db_password;
    private $charset = 'utf8mb4';
    private $pdo;

    public function __construct() {
        // Cargar variables de entorno, o valores por defecto para local
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->dbname = getenv('DB_DATABASE') ?: 'gymrat';
        $this->db_user = getenv('DB_USERNAME') ?: 'root';
        $this->db_password = getenv('DB_PASSWORD') ?: '';
    }

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

