<?php
require_once '../config/Database.php';

class InstructorController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Tomar lista de asistencia
    public function tomarLista($ficha_id, $ambiente_id, $instructor_id, $fecha, $estado) {
        $stmt = $this->db->prepare("INSERT INTO asistencias (ficha_id, ambiente_id, instructor_id, fecha, estado) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$ficha_id, $ambiente_id, $instructor_id, $fecha, $estado]);
    }

    // Obtener reportes de asistencias por ficha
    public function obtenerReportesPorFicha($ficha_id) {
        $stmt = $this->db->prepare("SELECT * FROM asistencias WHERE ficha_id = ?");
        $stmt->execute([$ficha_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>