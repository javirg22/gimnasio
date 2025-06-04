<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class Reserva {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        error_log("Reserva::__construct - Conexión DB establecida");
    }

    public function getReservasByUser($userId) {
        error_log("Reserva::getReservasByUser - Obteniendo reservas para usuario $userId");
        $query = "SELECT r.id_reserva, r.id_clase, c.nombre, c.fecha_hora 
                  FROM reservas r 
                  JOIN clases c ON r.id_clase = c.id_clase 
                  WHERE r.id_usuario = ? 
                  ORDER BY c.fecha_hora ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Reserva::getReservasByUser - Resultado: " . print_r($result, true));
        return $result;
    }

    public function reservadoEnUltimas24h($userId, $claseId) {
        error_log("Reserva::reservadoEnUltimas24h - Comprobando reserva en últimas 24h para usuario $userId y clase $claseId");
        $query = "SELECT COUNT(*) FROM reservas r
                  JOIN clases c ON r.id_clase = c.id_clase
                  WHERE r.id_usuario = ? AND r.id_clase = ? AND c.fecha_hora > (NOW() - INTERVAL 24 HOUR)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $claseId]);
        $count = $stmt->fetchColumn();
        $reservado = $count > 0;
        error_log("Reserva::reservadoEnUltimas24h - Resultado: $count reservas (reservado = " . ($reservado ? "Sí" : "No") . ")");
        return $reservado;
    }

    public function crearReserva($userId, $claseId) {
        error_log("Reserva::crearReserva - Intentando crear reserva para usuario $userId y clase $claseId");
        // Verificar que no exista reserva duplicada para esa clase y usuario (por si acaso)
        $queryCheck = "SELECT COUNT(*) FROM reservas WHERE id_usuario = ? AND id_clase = ?";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->execute([$userId, $claseId]);
        $count = $stmtCheck->fetchColumn();
        error_log("Reserva::crearReserva - Reservas duplicadas encontradas: $count");

        if ($count > 0) {
            error_log("Reserva::crearReserva - Ya existe una reserva para esta clase y usuario");
            return false; // Ya tiene reserva para esa clase
        }

        $query = "INSERT INTO reservas (id_usuario, id_clase) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([$userId, $claseId]);
        error_log("Reserva::crearReserva - Resultado insert: " . ($result ? "OK" : "FAIL"));
        return $result;
    }
    public function eliminarReserva($id_usuario, $id_clase)
{
    $query = "DELETE FROM reservas WHERE id_usuario = :id_usuario AND id_clase = :id_clase";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':id_clase', $id_clase, PDO::PARAM_INT);

    return $stmt->execute();
}

}
?>

