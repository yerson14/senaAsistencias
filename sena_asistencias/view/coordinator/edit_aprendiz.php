<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'coordinador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/AprendizModel.php';
require_once '../../models/FichaModel.php';
require_once '../../models/CentroModel.php';

$db = Database::getInstance()->getConnection();
$aprendizModel = new AprendizModel($db);
$fichaModel = new FichaModel($db);
$centroModel = new CentroModel($db);

$id = $_GET['id'];
$aprendiz = $aprendizModel->obtenerAprendizPorId($id);
$fichas = $fichaModel->obtenerFichas();
$centros = $centroModel->obtenerCentros();

if (!$aprendiz) {
    $_SESSION['error'] = "Aprendiz no encontrado";
    header("Location: create_aprendices.php");
    exit();
}
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

        <!-- Mensajes -->
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

        <!-- Formulario -->
        <form action="../../controllers/CoordinatorController.php?action=update_aprendiz&id=<?= $id ?>" method="POST" class="max-w-lg">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($aprendiz['nombre']) ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="numero_identificacion" class="block text-sm font-medium text-gray-700">Identificación</label>
                <input type="text" name="numero_identificacion" id="numero_identificacion" 
                       value="<?= htmlspecialchars($aprendiz['numero_identificacion']) ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="ficha_id" class="block text-sm font-medium text-gray-700">Ficha</label>
                <select name="ficha_id" id="ficha_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione ficha</option>
                    <?php foreach ($fichas as $ficha): ?>
                        <option value="<?= htmlspecialchars($ficha['id']) ?>" <?= ($ficha['id'] == $aprendiz['ficha_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ficha['codigo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro</label>
                <select name="centro_id" id="centro_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione centro</option>
                    <?php foreach ($centros as $centro): ?>
                        <option value="<?= htmlspecialchars($centro['id']) ?>" <?= ($centro['id'] == $aprendiz['centro_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($centro['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end">
                <a href="create_aprendices.php" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar</button>
            </div>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>