<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'coordinador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/AprendizModel.php';
require_once '../../models/FichaModel.php';

$db = Database::getInstance()->getConnection();
$aprendizModel = new AprendizModel($db);
$fichaModel = new FichaModel($db);

$id = $_GET['id'];
$aprendiz = $aprendizModel->obtenerAprendizPorId($id);
$fichas = $fichaModel->obtenerFichas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aprendiz</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Editar Aprendiz</h1>

        <!-- Mostrar mensajes de éxito o error -->
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

        <!-- Formulario para editar aprendiz -->
        <form action="../../controllers/CoordinatorController.php?action=update_aprendiz&id=<?php echo $id; ?>" method="POST">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo $aprendiz['nombre']; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="numero_identificacion" class="block text-sm font-medium text-gray-700">Número de Identificación</label>
                <input type="text" name="numero_identificacion" id="numero_identificacion" value="<?php echo $aprendiz['numero_identificacion']; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
    <label for="ficha_id" class="block text-sm font-medium text-gray-700">Ficha</label>
    <select name="ficha_id" id="ficha_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <option value="">Seleccione una ficha</option>
        <?php foreach ($fichas as $ficha): ?>
            <option value="<?php echo $ficha['id']; ?>" <?php echo ($ficha['id'] == $aprendiz['ficha_id']) ? 'selected' : ''; ?>>
                <?php echo $ficha['codigo']; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
            <div class="flex justify-end">
                <button type="button" onclick="window.history.back()" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>