<?php
session_start();
require 'includes/functions.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    redirect('login.php');
}

$user = $_SESSION['user'];

// Cerrar sesión si se hace clic en el botón de "Cerrar Sesión"
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    redirect('login.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50">
    <div class="container mx-auto p-6">
        <!-- Barra superior -->
        <div class="bg-green-700 text-white p-4 rounded-lg flex justify-between items-center shadow-md">
            <h1 class="text-2xl font-bold">Bienvenid@, <?php echo $user['username']; ?></h1>
            <a href="dashboard.php?logout=true" 
               class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                Cerrar Sesión
            </a>
        </div>

        <!-- Funcionalidades según el rol -->
        <div class="bg-white p-6 rounded-lg shadow-lg mt-6">
            <?php if ($user['role'] == 'super_admin'): ?>
                <h2 class="text-xl font-bold text-green-700 mb-4">Funciones de Super Administrador</h2>
                <ul class="space-y-2">
                    <li><a href="create_regional.php" class="text-green-600 hover:text-green-800 font-medium">➜ Crear Regional</a></li>
                    <li><a href="create_centro.php" class="text-green-600 hover:text-green-800 font-medium">➜ Crear Centro</a></li>
                    <li><a href="register.php" class="text-green-600 hover:text-green-800 font-medium">➜ Crear Coordinador</a></li>
                    <li><a href="view_regionales_centros.php" class="text-green-600 hover:text-green-800 font-medium">➜ Ver Regionales y Centros</a></li>  
                </ul>
            <?php elseif ($user['role'] == 'coordinator'): ?>
                <h2 class="text-xl font-bold text-green-700 mb-4">Funciones de Coordinador</h2>
                <ul class="space-y-2">
                    <li><a href="create_program.php" class="text-green-600 hover:text-green-800 font-medium">➜ Crear Programa</a></li>
                    <li><a href="create_ambiente.php" class="text-green-600 hover:text-green-800 font-medium">➜ Crear Ambiente</a></li>
                    <li><a href="create_ficha.php" class="text-green-600 hover:text-green-800 font-medium">➜ Crear Ficha</a></li>
                    <li><a href="create_instructor.php" class="text-green-600 hover:text-green-800 font-medium">➜ Agregar Instructor</a></li>
                    <li><a href="create_aprendiz.php" class="text-green-600 hover:text-green-800 font-medium">➜ Agregar Aprendiz</a></li>
                    <li><a href="reports.php" class="text-green-600 hover:text-green-800 font-medium">➜ Ver Reporte de Estudiantes</a></li>
                </ul>
            <?php elseif ($user['role'] == 'instructor'): ?>
                <h2 class="text-xl font-bold text-green-700 mb-4">Funciones de Instructor</h2>
                <ul class="space-y-2">
                    <li><a href="take_attendance.php" class="text-green-600 hover:text-green-800 font-medium">➜ Tomar Lista</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>