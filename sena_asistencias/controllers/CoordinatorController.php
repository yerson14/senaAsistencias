<?php
session_start(); // Iniciar la sesión
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/AmbienteModel.php'; // Incluir el modelo AmbienteModel
require_once __DIR__ . '/../models/CentroModel.php'; // Incluir el modelo CentroModel
require_once __DIR__ . '/../models/InstructorModel.php'; // Incluir el modelo InstructorModel

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

    public function createProgram($nombre, $codigo, $nivel_formacion)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($codigo) || empty($nivel_formacion)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Validar que el código sea un número
            if (!is_numeric($codigo)) {
                throw new Exception("El código del programa debe ser un número.");
            }

            // Usar el modelo ProgramaModel para crear el programa
            $programaFormacionModel = new ProgramaFormacionModel(0);
            if ($programaFormacionModel->crearPrograma($nombre, $codigo, $nivel_formacion)) {
                $_SESSION['success'] = "Programa creado exitosamente.";
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
                exit();
            } else {
                throw new Exception("Error al crear el programa.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_programa.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_programa.php");
            exit();
        }
    }

    public function crearFicha($numero, $programa_id, $fecha_inicio, $fecha_fin)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($numero) || empty($programa_id) || empty($fecha_inicio) || empty($fecha_fin)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Validar que el número y el programa_id sean números
            if (!is_numeric($numero) || !is_numeric($programa_id)) {
                throw new Exception("El número de ficha y el ID del programa deben ser números.");
            }

            // Validar que las fechas sean válidas
            if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
                throw new Exception("Las fechas ingresadas no son válidas.");
            }

            // Usar el modelo FichaModel para crear la ficha
            $fichaModel = new FichaModel();
            if ($fichaModel->crearFicha($numero, $programa_id, $fecha_inicio, $fecha_fin)) {
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
    public function obtenerCentros()
    {
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
    if ($action === 'create_ficha' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->crearAmbiente($nombre, $centro_id);
    }
    if ($action === 'crear_programa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
