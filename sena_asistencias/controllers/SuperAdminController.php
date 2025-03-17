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
    public function create_center() {
        // Verificar si se enviaron los datos del formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener los datos del formulario
            $nombre = $_POST['nombre'];
            $regional_id = $_POST['regional_id'];
    
            // Validar los datos (puedes agregar más validaciones si es necesario)
            if (empty($nombre) || empty($regional_id)) {
                $_SESSION['error'] = "Todos los campos son obligatorios.";
                header("Location: ../views/superadmin/crear_centro.php");
                exit();
            }
    
            // Guardar el centro en la base de datos
            $centroModel = new CentroModel(Database::getInstance()->getConnection());
            $centro_id = $centroModel->crearCentro($nombre, $regional_id);
    
            if ($centro_id) {
                // Redirigir a la página que muestra el centro creado
                $_SESSION['success'] = "Centro creado exitosamente.";
                header("Location: ../views/superadmin/mostrar_centro.php?id=" . $centro_id);
                exit();
            } else {
                $_SESSION['error'] = "Error al crear el centro.";
                header("Location: ../views/superadmin/crear_centro.php");
                exit();
            }
        }
    }

    // Método para editar una regional
    public function edit_regional($id, $nombre) {
        try {
            // Validar que el nombre no esté vacío
            if (empty($nombre)) {
                throw new Exception("El nombre de la regional es requerido.");
            }

            // Usar el modelo RegionalModel para editar la regional
            $regionalModel = new RegionalModel();
            if ($regionalModel->editarRegional($id, $nombre)) {
                $_SESSION['success'] = "Regional actualizada exitosamente.";
                header("Location: ../view/superadmin/index.php"); // Redirigir al dashboard
                exit();
            } else {
                throw new Exception("Error al actualizar la regional.");
            }
        } catch (Exception $e) {
            // Capturar errores y mostrar mensajes
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../view/superadmin/editar_regional.php?id=" . $id);
            exit();
        }
    }

    // Método para eliminar una regional
   // Método para eliminar una regional
public function delete_regional($id) {
    try {
        // Usar el modelo RegionalModel para eliminar la regional
        $regionalModel = new RegionalModel();
        if ($regionalModel->eliminarRegional($id)) {
            $_SESSION['success'] = "Regional eliminada exitosamente.";
        } else {
            throw new Exception("Error al eliminar la regional.");
        }
    } catch (Exception $e) {
        // Capturar errores y mostrar mensajes
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: ../view/superadmin/create_regional.php"); // Redirigir al dashboard
    exit();
}
// Método para editar un coordinador
public function edit_coordinador($id, $nombre, $correo, $numero_identificacion, $centro_id) {
    try {
        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id)) {
            throw new Exception("Todos los campos son requeridos.");
        }

        // Actualizar el coordinador en la tabla de usuarios
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, correo = ?, numero_identificacion = ? WHERE id = ?");
        if ($stmt->execute([$nombre, $correo, $numero_identificacion, $id])) {
            // Actualizar la asignación del coordinador a un centro
            $stmt = $this->db->prepare("UPDATE coordinadores SET centro_id = ? WHERE usuario_id = ?");
            if ($stmt->execute([$centro_id, $id])) {
                $_SESSION['success'] = "Coordinador actualizado exitosamente.";
                header("Location: ../view/superadmin/index.php");
                exit();
            } else {
                throw new Exception("Error al actualizar la asignación del coordinador al centro.");
            }
        } else {
            throw new Exception("Error al actualizar el coordinador.");
        }
    } catch (Exception $e) {
        // Capturar errores y mostrar mensajes
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../view/superadmin/editar_coordinador.php?id=" . $id);
        exit();
    }
}

// Método para eliminar un coordinador
public function delete_coordinador($id) {
    try {
        // Eliminar la asignación del coordinador al centro
        $stmt = $this->db->prepare("DELETE FROM coordinadores WHERE usuario_id = ?");
        if ($stmt->execute([$id])) {
            // Eliminar el coordinador de la tabla de usuarios
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['success'] = "Coordinador eliminado exitosamente.";
            } else {
                throw new Exception("Error al eliminar el coordinador.");
            }
        } else {
            throw new Exception("Error al eliminar la asignación del coordinador al centro.");
        }
    } catch (Exception $e) {
        // Capturar errores y mostrar mensajes
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: ../view/superadmin/index.php"); // Redirigir al dashboard
    exit();
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

    // Acción para editar una regional
    if ($action === 'edit_regional' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $superAdminController->edit_regional($id, $nombre);
    }

    // Acción para eliminar una regional
    if ($action === 'delete_regional' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $superAdminController->delete_regional($id);
    }
}
?>