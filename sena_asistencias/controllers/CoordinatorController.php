<?php
require_once '../config/Database.php';

class CoordinadorController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un programa de formación
    public function crearProgramaFormacion($nombre, $centro_id) {
        $stmt = $this->db->prepare("INSERT INTO programas_formacion (nombre, centro_id) VALUES (?, ?)");
        return $stmt->execute([$nombre, $centro_id]);
    }

    // Crear una ficha
    public function crearFicha($codigo, $programa_formacion_id) {
        $stmt = $this->db->prepare("INSERT INTO fichas (codigo, programa_formacion_id) VALUES (?, ?)");
        return $stmt->execute([$codigo, $programa_formacion_id]);
    }

    // Crear un ambiente
    public function crearAmbiente($nombre, $centro_id) {
        $stmt = $this->db->prepare("INSERT INTO ambientes (nombre, centro_id) VALUES (?, ?)");
        return $stmt->execute([$nombre, $centro_id]);
    }

    // Crear un instructor
    public function crearInstructor($usuario_id, $centro_id) {
        $stmt = $this->db->prepare("INSERT INTO instructores (usuario_id, centro_id) VALUES (?, ?)");
        return $stmt->execute([$usuario_id, $centro_id]);
    }

    // Obtener programas de formación por centro
    public function obtenerProgramasPorCentro($centro_id) {
        $stmt = $this->db->prepare("SELECT * FROM programas_formacion WHERE centro_id = ?");
        $stmt->execute([$centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener fichas por programa de formación
    public function obtenerFichasPorPrograma($programa_formacion_id) {
        $stmt = $this->db->prepare("SELECT * FROM fichas WHERE programa_formacion_id = ?");
        $stmt->execute([$programa_formacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ambientes por centro
    public function obtenerAmbientesPorCentro($centro_id) {
        $stmt = $this->db->prepare("SELECT * FROM ambientes WHERE centro_id = ?");
        $stmt->execute([$centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>