<?php
session_start();
require_once '../../config/Database.php';
require_once '../../models/ProgramaFormacionModel.php';

// Obtener los programas de formación desde la base de datos
$programaFormacionModel = new ProgramaFormacionModel(Database::getInstance()->getConnection());
$programas = $programaFormacionModel->obtenerProgramas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Ficha</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Crear Ficha</h1>

        <!-- Mostrar mensajes de error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensajes de éxito -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para crear una ficha -->
        <form action="../../controllers/CoordinatorController.php?action=create_ficha" method="POST">
            <div class="mb-4">
                <label for="numero" class="block text-sm font-medium text-gray-700">Número de Ficha</label>
                <input type="text" name="numero" id="numero" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="programa_id" class="block text-sm font-medium text-gray-700">Programa</label>
                <select name="programa_id" id="programa_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione un Programa</option>
                    <?php foreach ($programas as $programa): ?>
                        <option value="<?php echo $programa['id']; ?>"><?php echo $programa['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                Crear Ficha
            </button>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>