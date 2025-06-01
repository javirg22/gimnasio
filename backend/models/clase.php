<?php
class Clase {
    private $conn;
    public $id_clase;
    public $nombre;
    public $fecha_hora;
    public $aforo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllClases() {
        $query = "SELECT * FROM clases ORDER BY fecha_hora ASC";
        return $this->conn->query($query);
    }

    public function getClaseById($id) {
        $query = "SELECT * FROM clases WHERE id_clase = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_clase = $row['id_clase'];
            $this->nombre = $row['nombre'];
            $this->fecha_hora = $row['fecha_hora'];
            $this->aforo = $row['aforo'];
            return true;
        }
        return false;
    }

    public function reducirAforo($id) {
        $query = "UPDATE clases SET aforo = aforo - 1 WHERE id_clase = ? AND aforo > 0";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    public function incrementarAforo($id_clase)
{
    $query = "UPDATE clases SET aforo = aforo + 1 WHERE id_clase = :id_clase";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_clase', $id_clase, PDO::PARAM_INT);

    return $stmt->execute();
}

}
?>
