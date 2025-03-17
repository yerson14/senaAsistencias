<?php
session_start();
require_once '../config/Database.php';
require_once '../models/RegionalModel.php'; // Incluir el modelo RegionalModel
require_once '../models/CentroModel.php';  // Incluir el modelo CentroModel
require_once '../models/UsuarioModel.php'; // Incluir el modelo UsuarioModel

class SuperAdminController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Método para crear una regional
    public function create_regional($nombre)
    {
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

    // Método para crear un coordinador
    public function crear_coordinador($nombre, $correo, $numero_identificacion, $centro_id)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Insertar el coordinador en la tabla de usuarios
            $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, correo, numero_identificacion, rol) VALUES (?, ?, ?, 'coordinador')");
            if ($stmt->execute([$nombre, $correo, $numero_identificacion])) {
                // Obtener el ID del usuario recién creado
                $usuario_id = $this->db->lastInsertId();

                // Asignar el coordinador a un centro en la tabla coordinadores
                $stmt = $this->db->prepare("INSERT INTO coordinadores (usuario_id, centro_id) VALUES (?, ?)");
                if ($stmt->execute([$usuario_id, $centro_id])) {
                    $_SESSION['success'] = "Coordinador creado exitosamente.";
                    header("Location: ../view/superadmin/index.php"); // Redirigir al dashboard
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
            header("Location: ../view/superadmin/crear_coordinador.php"); // Redirigir al formulario de creación
            exit();
        }
    }

    // Método para editar una regional
    public function edit_regional($id, $nombre)
    {
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
    public function delete_regional($id)
    {
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

    // Método para crear un centro
    public function create_center($nombre, $regional_id)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre)) {
                throw new Exception("El nombre del centro es requerido.");
            }
            if (empty($regional_id)) {
                throw new Exception("Debe seleccionar una regional.");
            }

            // Usar el modelo CentroModel para crear el centro
            $centroModel = new CentroModel();
            if ($centroModel->crearCentro($nombre, $regional_id)) {
                $_SESSION['success'] = "Centro creado exitosamente.";
                header("Location: ../view/superadmin/index.php"); // Redirigir a la gestión de centros
                exit();
            } else {
                throw new Exception("Error al crear el centro.");
            }
        } catch (Exception $e) {
            // Capturar errores y mostrar mensajes
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../view/superadmin/create_center.php"); // Redirigir al formulario de creación
            exit();
        }
    }

    // Método para editar un coordinador
    public function edit_coordinador($id, $nombre, $correo, $numero_identificacion, $centro_id)
    {
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
    public function delete_coordinador($id)
    {
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

    // Acción para crear un coordinador
    if ($action === 'crear_coordinador' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $numero_identificacion = $_POST['numero_identificacion'];
        $centro_id = $_POST['centro_id'];
        $superAdminController->crear_coordinador($nombre, $correo, $numero_identificacion, $centro_id);
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