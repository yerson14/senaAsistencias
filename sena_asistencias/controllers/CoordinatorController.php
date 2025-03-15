<?php
session_start(); // Iniciar la sesión
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/AmbienteModel.php'; // Incluir el modelo AmbienteModel
require_once __DIR__ . '/../models/CentroModel.php'; // Incluir el modelo CentroModel
require_once __DIR__ . '/../models/InstructorModel.php'; // Incluir el modelo InstructorModel

class CoordinadorController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un ambiente
    public function crearAmbiente($nombre, $centro_id) {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Validar que el centro_id sea un número
            if (!is_numeric($centro_id)) {
                throw new Exception("El ID del centro debe ser un número.");
            }

            // Usar el modelo AmbienteModel para crear el ambiente
            $ambienteModel = new AmbienteModel();
            if ($ambienteModel->crearAmbiente($nombre, $centro_id)) {
                $_SESSION['success'] = "Ambiente creado exitosamente.";
                // Redirigir usando una ruta relativa desde la raíz del proyecto
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
                exit();
            } else {
                throw new Exception("Error al crear el ambiente.");
            }
        } catch (PDOException $e) {
            // Capturar errores de base de datos
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ambiente.php");
            exit();
        } catch (Exception $e) {
            // Capturar otros errores
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ambiente.php");
            exit();
        }
    }

    // Crear un instructor
    public function crearInstructor($nombre, $correo, $numero_identificacion, $centro_id) {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Validar que el centro_id sea un número
            if (!is_numeric($centro_id)) {
                throw new Exception("El ID del centro debe ser un número.");
            }

            // Usar el modelo InstructorModel para crear el instructor
            $instructorModel = new InstructorModel();
            if ($instructorModel->crearInstructor($nombre, $correo, $numero_identificacion, $centro_id)) {
                $_SESSION['success'] = "Instructor creado exitosamente.";
                // Redirigir usando una ruta relativa desde la raíz del proyecto
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
                exit();
            } else {
                throw new Exception("Error al crear el instructor.");
            }
        } catch (PDOException $e) {
            // Capturar errores de base de datos
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_instructor.php");
            exit();
        } catch (Exception $e) {
            // Capturar otros errores
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_instructor.php");
            exit();
        }
    }

    // Obtener todos los centros
    public function obtenerCentros() {
        $centroModel = new CentroModel();
        return $centroModel->obtenerCentros();
    }
}

// Manejar las acciones del controlador
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $coordinadorController = new CoordinadorController();

    // Acción para crear un ambiente
    if ($action === 'create_ambiente' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->crearAmbiente($nombre, $centro_id);
    }

    // Acción para crear un instructor
    if ($action === 'create_instructor' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $numero_identificacion = $_POST['numero_identificacion'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->crearInstructor($nombre, $correo, $numero_identificacion, $centro_id);
    }
}
?>