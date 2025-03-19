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

    public function getAprendicesByFicha($fichaId) {
        // Consulta para obtener los aprendices de la ficha seleccionada
        $query = "SELECT id, nombre, numero_identificacion FROM aprendices WHERE ficha_id = :ficha_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ficha_id', $fichaId, PDO::PARAM_INT);
        $stmt->execute();

        // Devolver los resultados en formato JSON
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

// Manejo de la acción 'get_aprendices'
if (isset($_GET['action']) && $_GET['action'] == 'get_aprendices') {
    if (isset($_GET['ficha_id'])) {
        $fichaId = $_GET['ficha_id'];
        $controller = new InstructorController();
        $controller->getAprendicesByFicha($fichaId);
    } else {
        echo json_encode(['error' => 'Ficha ID no proporcionado']);
    }
}
?>