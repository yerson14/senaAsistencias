<?php
require_once __DIR__ . '/../config/Database.php';

class CentroModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo centro
    public function crearCentro($nombre, $regional_id) {
        $sql = "INSERT INTO centros (nombre, regional_id) VALUES (:nombre, :regional_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':regional_id', $regional_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->db->lastInsertId(); // Retorna el ID del centro creado
        } 
        return false;
    }

    // Obtener centros por regional
    public function obtenerCentrosPorRegional($regional_id) {
        $sql = "SELECT * FROM centros WHERE regional_id = :regional_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':regional_id', $regional_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los centros
    public function obtenerCentros() {
        $sql = "SELECT * FROM centros";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un centro por ID
    public function obtenerCentroPorId($id) {
        $sql = "SELECT c.*, r.nombre AS regional_nombre 
                FROM centros c
                JOIN regionales r ON c.regional_id = r.id
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
