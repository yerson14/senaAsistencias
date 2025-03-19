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


if ($_GET['action'] == 'get_estudiantes' && isset($_GET['ficha_id'])) {
    $fichaId = $_GET['ficha_id'];
    
    // Aquí debes realizar la consulta a la base de datos para obtener los estudiantes de la ficha seleccionada
    // Por ejemplo:
    // $estudiantes = $db->query("SELECT nombre, apellido FROM estudiantes WHERE ficha_id = $fichaId")->fetchAll(PDO::FETCH_ASSOC);
    
    // Simulación de datos (reemplaza esto con la consulta real a la base de datos)
    $estudiantes = [
        ['nombre' => 'Juan', 'apellido' => 'Pérez'],
        ['nombre' => 'Ana', 'apellido' => 'Gómez'],
        // Agrega más estudiantes según la ficha seleccionada
    ];

    // Devuelve los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($estudiantes);
    exit;
}
?>
