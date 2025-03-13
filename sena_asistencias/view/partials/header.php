<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA - Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
<header class="fixed top-0 left-0 w-full bg-white shadow-md p-4 flex justify-between items-center z-50">
    <!-- Botón del menú hamburguesa -->
    <button id="menu-toggle" class="text-blue-500 hover:text-blue-700 focus:outline-none">
        <i class="fas fa-bars text-2xl"></i> <!-- Ícono de menú hamburguesa -->
    </button>

    <!-- Ícono de cerrar sesión -->
    <a href="../../controllers/AuthController.php?action=logout" class="text-blue-500 hover:text-blue-700">
        <i class="fas fa-sign-out-alt text-2xl"></i> <!-- Ícono de cerrar sesión -->
    </a>
</header>