<?php
session_start(); // Iniciar la sesión
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/AmbienteModel.php'; 
require_once __DIR__ . '/../models/CentroModel.php';
require_once __DIR__ . '/../models/InstructorModel.php';
require_once __DIR__ . '/../models/ProgramaFormacionModel.php';
require_once __DIR__ . '/../models/FichaModel.php';

class CoordinadorController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un ambiente
    public function crearAmbiente($nombre, $centro_id)
    {
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
            $ambienteModel = new AmbienteModel($this->db);
            if ($ambienteModel->crearAmbiente($nombre, $centro_id)) {
                $_SESSION['success'] = "Ambiente creado exitosamente.";
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
                exit();
            } else {
                throw new Exception("Error al crear el ambiente.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ambiente.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ambiente.php");
            exit();
        }
    }

    // Método para crear un programa
    public function createProgram($nombre, $centro_id)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Validar que el centro_id sea un número
            if (!is_numeric($centro_id)) {
                throw new Exception("El ID del centro debe ser un número.");
            }

            // Usar el modelo ProgramaFormacionModel para crear el programa
            $programaFormacionModel = new ProgramaFormacionModel($this->db);
            if ($programaFormacionModel->crearPrograma($nombre, $centro_id)) {
                $_SESSION['success'] = "Programa creado exitosamente.";
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
                exit();
            } else {
                throw new Exception("Error al crear el programa.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_program.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_program.php");
            exit();
        }
    }

    public function crearFicha($numero, $programa_id)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($numero) || empty($programa_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Validar que el número y el programa_id sean números
            if (!is_numeric($numero) || !is_numeric($programa_id)) {
                throw new Exception("El número de ficha y el ID del programa deben ser números.");
            }

          

            // Usar el modelo FichaModel para crear la ficha
            $fichaModel = new FichaModel($this->db);
            if ($fichaModel->crearFicha($numero, $programa_id)) {
                $_SESSION['success'] = "Ficha creada exitosamente.";
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
                exit();
            } else {
                throw new Exception("Error al crear la ficha.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ficha.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ficha.php");
            exit();
        }
    }

    // Crear un instructor
    public function crearInstructor($nombre, $correo, $numero_identificacion, $centro_id)
    {
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
            $instructorModel = new InstructorModel($this->db);
            if ($instructorModel->crearInstructor($nombre, $correo, $numero_identificacion, $centro_id)) {
                $_SESSION['success'] = "Instructor creado exitosamente.";
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
                exit();
            } else {
                throw new Exception("Error al crear el instructor.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_instructor.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_instructor.php");
            exit();
        }
    }

    // Obtener todos los centros
    public function obtenerCentros()
    {
        $centroModel = new CentroModel($this->db);
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
    
    // Acción para crear una ficha
    if ($action === 'create_ficha' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $numero = $_POST['numero'];
        $programa_id = $_POST['programa_id'];
        $coordinadorController->crearFicha($numero, $programa_id);
    }
    
    // Acción para crear un programa
    if ($action === 'create_program' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->createProgram($nombre, $centro_id);
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