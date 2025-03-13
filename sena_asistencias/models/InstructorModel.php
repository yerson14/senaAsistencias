<?php
require_once '../config/Database.php';

class InstructorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo instructor
    public function crearInstructor($usuario_id, $centro_id) {
        $stmt = $this->db->prepare("INSERT INTO instructores (usuario_id, centro_id) VALUES (?, ?)");
        return $stmt->execute([$usuario_id, $centro_id]);
    }

    // Obtener instructores por centro
    public function obtenerInstructoresPorCentro($centro_id) {
        $stmt = $this->db->prepare("SELECT * FROM instructores WHERE centro_id = ?");
        $stmt->execute([$centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>