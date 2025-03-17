<?php
require_once __DIR__ . '/../config/Database.php';

class AmbienteModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo ambiente
    public function crearAmbiente($nombre, $centro_id) {
        try {
            $stmt = $this->db->prepare("INSERT INTO ambientes (nombre, centro_id) VALUES (?, ?)");
            return $stmt->execute([$nombre, $centro_id]);
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Obtener ambientes por centro
    public function obtenerAmbientesPorCentro($centro_id) {
        $stmt = $this->db->prepare("SELECT * FROM ambientes WHERE centro_id = ?");
        $stmt->execute([$centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los ambientes
    public function obtenerAmbientes() {
        $stmt = $this->db->prepare("SELECT * FROM ambientes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>