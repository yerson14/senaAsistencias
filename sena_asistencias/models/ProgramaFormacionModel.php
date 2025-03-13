<?php
require_once '../config/Database.php';

class ProgramaFormacionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo programa de formación
    public function crearProgramaFormacion($nombre, $centro_id) {
        $stmt = $this->db->prepare("INSERT INTO programas_formacion (nombre, centro_id) VALUES (?, ?)");
        return $stmt->execute([$nombre, $centro_id]);
    }

    // Obtener programas de formación por centro
    public function obtenerProgramasPorCentro($centro_id) {
        $stmt = $this->db->prepare("SELECT * FROM programas_formacion WHERE centro_id = ?");
        $stmt->execute([$centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>