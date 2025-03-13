<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: view/auth/login.php");
    exit();
}

// Redirigir según el rol del usuario
$rol = $_SESSION['usuario']['rol'];
switch ($rol) {
    case 'superadmin':
        header("Location: view/superadmin/index.php");
        break;
    case 'coordinador':
        header("Location: view/coordinator/index.php");
        break;
    case 'instructor':
        header("Location: view/instructor/index.php");
        break;
    default:
        header("Location: view/auth/login.php");
        break;
}
exit();
?>