<?php
require_once '../config/Database.php';

class RegionalModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear una nueva regional
    public function crearRegional($nombre) {
        $stmt = $this->db->prepare("INSERT INTO regionales (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    // Obtener todas las regionales
    public function obtenerRegionales() {
        $stmt = $this->db->query("SELECT * FROM regionales");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>