<?php
require_once __DIR__ . '/../config/Database.php';

class CentroModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo centro
    public function crearCentro($nombre, $regional_id) {
        $stmt = $this->db->prepare("INSERT INTO centros (nombre, regional_id) VALUES (?, ?)");
        return $stmt->execute([$nombre, $regional_id]);
    }

    // Obtener centros por regional
    public function obtenerCentrosPorRegional($regional_id) {
        $stmt = $this->db->prepare("SELECT * FROM centros WHERE regional_id = ?");
        $stmt->execute([$regional_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los centros
    public function obtenerCentros() {
        $stmt = $this->db->query("SELECT * FROM centros");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>