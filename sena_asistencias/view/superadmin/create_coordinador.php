<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/CentroModel.php';
require_once __DIR__ . '/../../models/UsuarioModel.php';

// Verificar si el usuario es superadmin
if ($_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../../index.php');
    exit;
}

$centroModel = new CentroModel();
$coordinadorModel = new UsuarioModel();

// Obtener los centros disponibles
$centros = $centroModel->obtenerCentros();

// Obtener la lista de coordinadores
$coordinadores = $coordinadorModel->obtenerCoordinadores();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Coordinadores</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Gestión de Coordinadores</h1>

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

        <!-- Botón flotante para agregar coordinador -->
        <button onclick="openModal()" class="fixed bottom-8 right-8 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition duration-300">
            <i class="fas fa-plus text-2xl"></i>
        </button>

        <!-- Modal para agregar coordinador -->
        <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Agregar Coordinador</h2>
                <form action="../../controllers/SuperAdminController.php?action=crear_coordinador" method="POST">
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="numero_identificacion" class="block text-sm font-medium text-gray-700">Número de Identificación</label>
                        <input type="text" name="numero_identificacion" id="numero_identificacion" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
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
                    <div class="flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de coordinadores en cuadros -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($coordinadores as $coordinador): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2"><?php echo $coordinador['nombre']; ?></h2>
                    <p class="text-gray-600">Correo: <?php echo $coordinador['correo']; ?></p>
                    <p class="text-gray-600">Centro: <?php echo $coordinador['centro_nombre']; ?></p>
                    <div class="mt-4 flex space-x-2">
                        <a href="editar_coordinador.php?id=<?php echo $coordinador['id']; ?>" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="../../controllers/SuperAdminController.php?action=delete_coordinador&id=<?php echo $coordinador['id']; ?>" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Funciones para abrir y cerrar el modal
        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
    </script>

    <?php include '../partials/footer.php'; ?>
</body>
</html>