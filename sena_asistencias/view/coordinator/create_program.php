<?php
session_start(); // Iniciar la sesión para manejar mensajes de éxito/error
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/CentroModel.php';

// Obtener los centros desde la base de datos
$centroModel = new CentroModel(Database::getInstance()->getConnection());
$centros = $centroModel->obtenerCentros();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Programa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Crear Programa</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <form action="../../controllers/CoordinatorController.php?action=create_program" method="POST">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Programa</label>
                <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro</label>
                <select name="centro_id" id="centro_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione un centro</option>
                    <?php foreach ($centros as $centro): ?>
                        <option value="<?php echo $centro['id']; ?>"><?php echo $centro['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                Crear Programa
            </button>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>