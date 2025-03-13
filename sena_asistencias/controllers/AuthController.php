<?php
session_start();
require_once '../config/Database.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($numero_identificacion) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE numero_identificacion = ?");
        $stmt->execute([$numero_identificacion]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['usuario'] = $usuario;

            // Redirigir según el rol del usuario
            switch ($usuario['rol']) {
                case 'superadmin':
                    header("Location: ../view/superadmin/index.php");
                    break;
                case 'coordinador':
                    header("Location: ../view/coordinator/index.php");
                    break;
                case 'instructor':
                    header("Location: ../view/instructor/index.php");
                    break;
                default:
                    // Si el rol no es válido, redirigir al login
                    header("Location: ../view/auth/login.php");
                    break;
            }
            exit(); // Asegúrate de salir después de redirigir
        } else {
            // Si el usuario no existe, redirigir al login con un mensaje de error
            $_SESSION['error'] = "Número de identificación no encontrado.";
            header("Location: ../view/auth/login.php");
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: ../view/auth/login.php");
        exit();
    }
}

// Manejar la acción de login
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $authController = new AuthController();

    if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $numero_identificacion = $_POST['numero_identificacion'];
        $authController->login($numero_identificacion);
    } elseif ($action === 'logout') {
        $authController->logout();
    }
}
?>