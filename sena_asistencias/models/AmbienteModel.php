<?php
require_once '../config/Database.php';

class AmbienteModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo ambiente
    public function crearAmbiente($nombre, $centro_id) {
        $stmt = $this->db->prepare("INSERT INTO ambientes (nombre, centro_id) VALUES (?, ?)");
        return $stmt->execute([$nombre, $centro_id]);
    }

    // Obtener ambientes por centro
    public function obtenerAmbientesPorCentro($centro_id) {
        $stmt = $this->db->prepare("SELECT * FROM ambientes WHERE centro_id = ?");
        $stmt->execute([$centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>