<?php
session_start();
require_once '../config/Database.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($numero_identificacion, $correo) {
        // Validar que ambos campos estén presentes
        if (empty($numero_identificacion)) {
            $_SESSION['error'] = "El número de identificación es requerido.";
            header("Location: ../view/auth/login.php");
            exit();
        }

        if (empty($correo)) {
            $_SESSION['error'] = "El correo electrónico es requerido.";
            header("Location: ../vie/auth/login.php");
            exit();
        }

        // Buscar al usuario en la base de datos
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE numero_identificacion = ? AND correo = ?");
        $stmt->execute([$numero_identificacion, $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Guardar el usuario en la sesión
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
                    $_SESSION['error'] = "Rol no válido.";
                    header("Location: ../view/auth/login.php");
                    break;
            }
            exit(); // Asegúrate de salir después de redirigir
        } else {
            // Si el usuario no existe, redirigir al login con un mensaje de error
            $_SESSION['error'] = "Número de identificación o correo incorrectos.";
            header("Location: ../view/auth/login.php");
            exit();
        }
    }

    public function logout() {
        // Destruir la sesión y redirigir al login
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
        // Obtener los datos del formulario
        $numero_identificacion = $_POST['numero_identificacion'];
        $correo = $_POST['correo'];

        // Llamar al método login
        $authController->login($numero_identificacion, $correo);
    } elseif ($action === 'logout') {
        // Llamar al método logout
        $authController->logout();
    }
}
?>