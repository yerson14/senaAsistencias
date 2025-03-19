<?php
require_once '../config/Database.php';

class AprendizModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerAprendicesPorFicha($ficha_id) {
        $query = "SELECT id, nombre FROM aprendices WHERE ficha_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $ficha_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $aprendices = [];
        while ($row = $result->fetch_assoc()) {
            $aprendices[] = $row;
        }
        return $aprendices;
    }
}
