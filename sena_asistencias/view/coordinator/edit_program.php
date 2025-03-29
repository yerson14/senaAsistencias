<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'coordinador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/ProgramaFormacionModel.php';
require_once '../../models/CentroModel.php';

$programaModel = new ProgramaFormacionModel(Database::getInstance()->getConnection());
$centroModel = new CentroModel(Database::getInstance()->getConnection());

$id = $_GET['id'];
$programa = $programaModel->obtenerProgramaPorIdConRegional($id);
$centro = $centroModel->obtenerCentroPorId($programa['centro_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Programa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8 flex-grow">
        <h1 class="text-3xl font-bold mb-6">Editar Programa</h1>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="../../controllers/CoordinatorController.php?action=update_program&id=<?= $id ?>" method="POST">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Programa</label>
                <input type="text" name="nombre" id="nombre" 
                       value="<?= htmlspecialchars($programa['nombre']) ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Regional</label>
                <div class="mt-1 p-2 bg-gray-100 rounded-md">
                    <?= htmlspecialchars($programa['regional_nombre']) ?>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Centro</label>
                <div class="mt-1 p-2 bg-gray-100 rounded-md">
                    <?= htmlspecialchars($programa['centro_nombre']) ?>
                </div>
                <input type="hidden" name="centro_id" value="<?= $programa['centro_id'] ?>">
            </div>
            
            <div class="flex justify-end">
                <a href="create_program.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition ml-2">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>