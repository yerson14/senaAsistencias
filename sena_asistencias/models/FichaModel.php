<?php
require_once '../config/Database.php';

class FichaModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearFicha($numero, $programa_id, $fecha_inicio, $fecha_fin) {
        $stmt = $this->db->prepare("INSERT INTO fichas (codigo, programa_formacion_id, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$numero, $programa_id, $fecha_inicio, $fecha_fin]);
    }

    public function obtenerFichasPorPrograma($programa_id) {
        $stmt = $this->db->prepare("SELECT * FROM fichas WHERE programa_formacion_id = ?");
        $stmt->execute([$programa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>