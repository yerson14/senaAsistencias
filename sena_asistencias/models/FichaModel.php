<?php
require_once '../config/Database.php';

class FichaModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear una nueva ficha
    public function crearFicha($codigo, $programa_formacion_id) {
        $stmt = $this->db->prepare("INSERT INTO fichas (codigo, programa_formacion_id) VALUES (?, ?)");
        return $stmt->execute([$codigo, $programa_formacion_id]);
    }

    // Obtener fichas por programa de formación
    public function obtenerFichasPorPrograma($programa_formacion_id) {
        $stmt = $this->db->prepare("SELECT * FROM fichas WHERE programa_formacion_id = ?");
        $stmt->execute([$programa_formacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>