<?php
require_once '../config/Database.php';

class InstructorController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerAprendices($ficha_id) {
        $stmt = $this->db->prepare("SELECT id, nombre AS name FROM aprendices WHERE ficha_id = ?");
        $stmt->execute([$ficha_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFichas() {
        $stmt = $this->db->prepare("SELECT id, nombre AS name FROM fichas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAmbientes() {
        $stmt = $this->db->prepare("SELECT id, nombre AS name FROM ambientes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>