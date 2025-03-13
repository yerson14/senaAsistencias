<?php
session_start();
require_once '../config/Database.php';
require_once '../models/RegionalModel.php'; // Incluir el modelo RegionalModel
require_once '../models/UsuarioModel.php'; // Incluir el modelo UsuarioModel

class SuperAdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Método para crear una regional
    public function create_regional($nombre) {
        try {
            // Validar que el nombre no esté vacío
            if (empty($nombre)) {
                throw new Exception("El nombre de la regional es requerido.");
            }

            // Usar el modelo RegionalModel para crear la regional
            $regionalModel = new RegionalModel();
            if ($regionalModel->crearRegional($nombre)) {
                $_SESSION['success'] = "Regional creada exitosamente.";
                header("Location: ../view/superadmin/index.php"); // Redirigir al dashboard
                exit();
            } else {
                throw new Exception("Error al crear la regional.");
            }
        } catch (Exception $e) {
            // Capturar errores y mostrar mensajes
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../view/superadmin/create_regional.php");
            exit();
        }
    }

    // Método para crear un centro
    public function create_center($nombre, $regional_id) {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre)) {
                throw new Exception("El nombre del centro es requerido.");
            }

            if (empty($regional_id)) {
                throw new Exception("Debe seleccionar una regional.");
            }

            // Insertar el centro en la base de datos
            $stmt = $this->db->prepare("INSERT INTO centros (nombre, regional_id) VALUES (?, ?)");
            if ($stmt->execute([$nombre, $regional_id])) {
                $_SESSION['success'] = "Centro creado exitosamente.";
                header("Location: ../view/superadmin/index.php"); // Redirigir al dashboard
                exit();
            } else {
                throw new Exception("Error al crear el centro.");
            }
        } catch (Exception $e) {
            // Capturar errores y mostrar mensajes
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../view/superadmin/create_center.php");
            exit();
        }
    }

    // Método para crear un coordinador
    public function create_coordinador($nombre, $correo, $numero_identificacion, $centro_id) {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Insertar el coordinador en la tabla de usuarios
            $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, correo, numero_identificacion, rol) VALUES (?, ?, ?, 'coordinador')");
            if ($stmt->execute([$nombre, $correo, $numero_identificacion])) {
                $usuario_id = $this->db->lastInsertId(); // Obtener el ID del usuario creado

                // Asignar el coordinador a un centro
                $stmt = $this->db->prepare("INSERT INTO coordinadores (usuario_id, centro_id) VALUES (?, ?)");
                if ($stmt->execute([$usuario_id, $centro_id])) {
                    $_SESSION['success'] = "Coordinador creado exitosamente.";
                    header("Location: ../view/superadmin/index.php");
                    exit();
                } else {
                    throw new Exception("Error al asignar el coordinador al centro.");
                }
            } else {
                throw new Exception("Error al crear el coordinador.");
            }
        } catch (Exception $e) {
            // Capturar errores y mostrar mensajes
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../view/superadmin/create_coordinador.php");
            exit();
        }
    }
}

// Manejar las acciones del controlador
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $superAdminController = new SuperAdminController();

    // Acción para crear una regional
    if ($action === 'create_regional' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $superAdminController->create_regional($nombre);
    }

    // Acción para crear un centro
    if ($action === 'create_center' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $regional_id = $_POST['regional_id'];
        $superAdminController->create_center($nombre, $regional_id);
    }

    // Acción para crear un coordinador
    if ($action === 'create_coordinador' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $numero_identificacion = $_POST['numero_identificacion'];
        $centro_id = $_POST['centro_id'];
        $superAdminController->create_coordinador($nombre, $correo, $numero_identificacion, $centro_id);
    }
}
?>