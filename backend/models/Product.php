<?php
class Product {
    private $conn;
    private $table_name = "productos";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los productos
    public function getAllProducts() {
        $query = "SELECT 
                    id_producto AS id, 
                    nombre AS name, 
                    descripcion AS description, 
                    precio AS price, 
                    imagen AS image_url 
                  FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear un nuevo producto
    public function createProduct($name, $description, $price, $image_url) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, descripcion, precio, , imagen) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $price);
        
        $stmt->bindParam(5, $image_url);

        return $stmt->execute();
    }

    

}
?>


