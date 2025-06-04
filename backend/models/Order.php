<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class Order {
    private $conn;
    private $table_name = "pedidos";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear un nuevo pedido
    public function createOrder($user_id, $total_price, $status) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_usuario, total, estado) 
                  VALUES (:user_id, :total_price, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // Obtener un pedido por ID
    public function getOrderById($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_pedido = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener pedidos por usuario
    public function getOrdersByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_usuario = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar estado del pedido
    public function updateOrderStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = :status 
                  WHERE id_pedido = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // Eliminar pedido
    public function deleteOrder($id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id_pedido = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
