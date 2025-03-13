<?php
require_once '../config/Database.php';

class SuperAdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear una nueva regional
    public function crearRegional($nombre) {
        $stmt = $this->db->prepare("INSERT INTO regionales (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    // Crear un nuevo centro
    public function crearCentro($nombre, $regional_id) {
        $stmt = $this->db->prepare("INSERT INTO centros (nombre, regional_id) VALUES (?, ?)");
        return $stmt->execute([$nombre, $regional_id]);
    }

    // Obtener todas las regionales
    public function obtenerRegionales() {
        $stmt = $this->db->query("SELECT * FROM regionales");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener centros por regional
    public function obtenerCentrosPorRegional($regional_id) {
        $stmt = $this->db->prepare("SELECT * FROM centros WHERE regional_id = ?");
        $stmt->execute([$regional_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>