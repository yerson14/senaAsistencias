<?php
session_start();
require_once '../../config/Database.php';
require_once '../../models/CentroModel.php';
require_once '../../models/RegionalModel.php';

// Verificar si el usuario es superadmin
if ($_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../../index.php');
    exit;
}

// Obtener el ID del centro a editar
if (!isset($_GET['id'])) {
    header('Location: ../view/superadmin/create_center.php');
    exit;
}

$id = $_GET['id'];

// Obtener los datos del centro
$centroModel = new CentroModel(Database::getInstance()->getConnection());
$centro = $centroModel->obtenerCentroPorId($id);

if (!$centro) {
    $_SESSION['error'] = "Centro no encontrado.";
    header('Location: ../view/superadmin/create_center.php');
    exit;
}

// Obtener las regionales disponibles
$regionalModel = new RegionalModel(Database::getInstance()->getConnection());
$regionales = $regionalModel->obtenerRegionales();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Centro</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Editar Centro</h1>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para editar el centro -->
        <form action="../../controllers/SuperAdminController.php?action=edit_center" method="POST">
            <input type="hidden" name="id" value="<?php echo $centro['id']; ?>">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Centro</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo $centro['nombre']; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label>
                <select name="regional_id" id="regional_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione una regional</option>
                    <?php foreach ($regionales as $regional): ?>
                        <option value="<?php echo $regional['id']; ?>" <?php echo ($regional['id'] == $centro['regional_id']) ? 'selected' : ''; ?>>
                            <?php echo $regional['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>