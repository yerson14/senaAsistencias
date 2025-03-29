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
require_once '../../models/RegionalModel.php';

$db = Database::getInstance()->getConnection();
$aprendizModel = new AprendizModel($db);
$fichaModel = new FichaModel($db);
$centroModel = new CentroModel($db);
$regionalModel = new RegionalModel($db);

$regionales = $regionalModel->obtenerRegionales();
$aprendices = $aprendizModel->obtenerAprendices();
$fichas = $fichaModel->obtenerFichas();
$centros = $centroModel->obtenerCentros();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aprendices</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .modal-container {
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Gestión de Aprendices</h1>

        <!-- Mensajes de éxito/error -->
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

        <!-- Botón flotante para agregar -->
        <button onclick="openModal()" class="fixed bottom-8 right-8 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition">
            <i class="fas fa-plus text-2xl"></i>
        </button>

        <!-- Modal para agregar -->
       
        <div id="modal" class="hidden modal-container fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Agregar Aprendiz</h2>
                <form action="../../controllers/CoordinatorController.php?action=create_aprendiz" method="POST">
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="numero_identificacion" class="block text-sm font-medium text-gray-700">Identificación</label>
                        <input type="text" name="numero_identificacion" id="numero_identificacion" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="ficha_id" class="block text-sm font-medium text-gray-700">Ficha</label>
                        <select name="ficha_id" id="ficha_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Seleccione ficha</option>
                            <?php foreach ($fichas as $ficha): ?>
                                <option value="<?= htmlspecialchars($ficha['id']) ?>"><?= htmlspecialchars($ficha['codigo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro</label>
                        <select name="centro_id" id="centro_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Seleccione centro</option>
                            <?php foreach ($centros as $centro): ?>
                                <option value="<?= htmlspecialchars($centro['id']) ?>"><?= htmlspecialchars($centro['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
    <label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label>
    <select name="regional_id" id="regional_id" 
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
            required
            onchange="cargarCentrosPorRegional(this.value)">
        <option value="">Seleccione regional</option>
        <?php foreach ($regionales as $regional): ?>
            <option value="<?= htmlspecialchars($regional['id']) ?>">
                <?= htmlspecialchars($regional['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de aprendices -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($aprendices as $aprendiz): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($aprendiz['nombre']) ?></h2>
                    <p class="text-gray-600">ID: <?= htmlspecialchars($aprendiz['numero_identificacion']) ?></p>
                    <p class="text-gray-600">Ficha: <?= htmlspecialchars($aprendiz['ficha_numero']) ?></p>
                    <div class="mt-4 flex space-x-2">
                        <a href="edit_aprendiz.php?id=<?= $aprendiz['id'] ?>" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="../../controllers/CoordinatorController.php?action=delete_aprendiz&id=<?= $aprendiz['id'] ?>" 
                           class="text-red-500 hover:text-red-700" 
                           onclick="return confirm('¿Eliminar este aprendiz?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>

    <?php include '../partials/footer.php'; ?>
</body>
</html>