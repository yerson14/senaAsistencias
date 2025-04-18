<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'coordinador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/AmbienteModel.php';
require_once '../../models/CentroModel.php';
require_once '../../models/RegionalModel.php';

$db = Database::getInstance()->getConnection();

$ambienteModel = new AmbienteModel($db);
$centroModel = new CentroModel($db);
$regionalModel = new RegionalModel();

// Obtener ambientes con información de centro y regional
$ambientes = $ambienteModel->obtenerAmbientesCompletos();
$centros = $centroModel->obtenerCentrosConRegional();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ambientes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Gestión de Ambientes</h1>

        <!-- Mensajes -->
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

        <!-- Botón flotante -->
        <button onclick="openModal()" class="fixed bottom-8 right-8 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition duration-300 z-50">
            <i class="fas fa-plus text-2xl"></i>
        </button>

        <!-- Modal para agregar ambiente -->
        <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Agregar Ambiente</h2>
                <form action="../../controllers/CoordinatorController.php?action=create_ambiente" method="POST">
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Ambiente</label>
                        <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label>
                        <select name="regional_id" id="regional_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required onchange="cargarCentros(this.value)">
                            <option value="">Seleccione una regional</option>
                            <?php 
                            $regionales = $regionalModel->obtenerRegionales();
                            foreach ($regionales as $regional): ?>
                                <option value="<?php echo $regional['id']; ?>"><?php echo $regional['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro</label>
                        <select name="centro_id" id="centro_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Seleccione un centro</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de ambientes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($ambientes as $ambiente): ?>
                <div class="border-2 border-green-500 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2"><?php echo $ambiente['nombre']; ?></h2>
                    <p class="text-gray-600">ID: <?php echo $ambiente['id']; ?></p>
                    <p class="text-gray-600">Centro: <?php echo $ambiente['centro_nombre']; ?></p>
                    <p class="text-gray-600">Regional: <?php echo $ambiente['regional_nombre']; ?></p>
                    <div class="mt-4 flex space-x-2">
                        <a href="edit_ambiente.php?id=<?php echo $ambiente['id']; ?>"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105 flex items-center space-x-2">
                            <i class="fas fa-edit"></i>
                            <span>Editar</span>
                        </a>
                        <a href="../../controllers/CoordinatorController.php?action=delete_ambiente&id=<?php echo $ambiente['id']; ?>"
                            class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105 flex items-center space-x-2"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar este ambiente?');">
                            <i class="fas fa-trash"></i>
                            <span>Eliminar</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
        
        function cargarCentros(regional_id) {
            if (regional_id) {
                $.ajax({
                    url: '../../controllers/CoordinatorController.php?action=get_centros&regional_id=' + regional_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var select = $('#centro_id');
                        select.empty();
                        select.append('<option value="">Seleccione un centro</option>');
                        
                        $.each(data, function(index, centro) {
                            select.append('<option value="' + centro.id + '">' + centro.nombre + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar centros:', error);
                    }
                });
            } else {
                $('#centro_id').empty().append('<option value="">Seleccione un centro</option>');
            }
        }
    </script>

    <?php include '../partials/footer.php'; ?>
</body>
</html>