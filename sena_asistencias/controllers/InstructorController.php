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
    require_once '../../models/AsistenciaModel.php';

class InstructorController {
    private $asistenciaModel;

    public function __construct() {
        $this->asistenciaModel = new AsistenciaModel();
    }

    // Guardar la asistencia
    public function takeAttendance() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ficha_id = $_POST['ficha_id'];
            $programa_formacion_id = $_POST['programa_formacion'];
            $ambiente_id = $_POST['ambiente_id'];
            $fecha = $_POST['fecha'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];
            $asistencia = json_decode($_POST['asistencia'], true);

            foreach ($asistencia as $aprendiz_id => $estado) {
                $this->asistenciaModel->guardarAsistencia(
                    $ficha_id,
                    $programa_formacion_id,
                    $ambiente_id,
                    $fecha,
                    $hora_inicio,
                    $hora_fin,
                    $aprendiz_id,
                    $estado
                );
            }

            echo json_encode(['success' => true]);
        }
    }

    // Obtener los aprendices de una ficha
    public function getAprendices() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ficha_id'])) {
            $ficha_id = $_GET['ficha_id'];
            $aprendices = $this->asistenciaModel->obtenerAprendicesPorFicha($ficha_id);

            echo json_encode($aprendices);
        }
    }
}

// Manejar la acción solicitada
if (isset($_GET['action'])) {
    $controller = new InstructorController();
    $action = $_GET['action'];

    if ($action === 'take_attendance') {
        $controller->takeAttendance();
    } elseif ($action === 'get_aprendices') {
        $controller->getAprendices();
    }
}
}

?>