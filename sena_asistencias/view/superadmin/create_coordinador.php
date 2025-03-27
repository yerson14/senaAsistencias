<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/CentroModel.php';
require_once __DIR__ . '/../../models/UsuarioModel.php';
require_once __DIR__ . '/../../models/RegionalModel.php';

// Verificar si el usuario es superadmin
if ($_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../../index.php');
    exit;
}

// Inicializar modelos
$centroModel = new CentroModel();
$usuarioModel = new UsuarioModel();
$regionalModel = new RegionalModel();

// Obtener datos necesarios
$centros = $centroModel->obtenerCentros();
$coordinadores = $usuarioModel->obtenerCoordinadores();
$regionales = $regionalModel->obtenerRegionales();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Coordinadores</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .modal-container { z-index: 1000; }
        .main-content { min-height: calc(100vh - 120px); }
        select:disabled { background-color: #f3f4f6; cursor: not-allowed; }
        #centro-container { transition: all 0.3s ease; }
        .hidden { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8 flex-grow main-content">
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
        <button onclick="openModal()" class="fixed bottom-8 right-8 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition duration-300 z-40">
            <i class="fas fa-plus text-2xl"></i>
        </button>

        <!-- Modal para agregar coordinador -->
        <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 modal-container">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Agregar Coordinador</h2>
                <form id="formCoordinador" action="../../controllers/SuperAdminController.php?action=crear_coordinador" method="POST">
                    <!-- Campos del formulario (nombre, correo, identificación) -->
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="correo" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <input type="email" name="correo" id="correo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="numero_identificacion" class="block text-sm font-medium text-gray-700">Número de identificación</label>
                        <input type="text" name="numero_identificacion" id="numero_identificacion" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <!-- Selector de Regional -->
                    <div class="mb-4">
                        <label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label>
                        <select name="regional_id" id="regional_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required onchange="cargarCentrosPorRegional(this.value)">
                            <option value="">Seleccione una regional</option>
                            <?php foreach ($regionales as $regional): ?>
                                <option value="<?= htmlspecialchars($regional['id']) ?>">
                                    <?= htmlspecialchars($regional['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Contenedor de Centros -->
                    <div id="centro-container" class="mb-4 hidden">
                        <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro de formación</label>
                        <select name="centro_id" id="centro_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required disabled>
                            <option value="">Seleccione un centro</option>
                        </select>
                        <div id="centro-mensaje" class="mt-2 text-sm text-red-600 hidden"></div>
                    </div>
                    
                    <!-- Botones del formulario -->
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Cancelar</button>
                        <button type="submit" id="submit-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50" disabled>Guardar Coordinador</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de coordinadores en cuadros -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($coordinadores as $coordinador): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($coordinador['nombre']) ?></h2>
                    <p class="text-gray-600">Correo: <?= htmlspecialchars($coordinador['correo']) ?></p>
                    <p class="text-gray-600">Regional: <?= htmlspecialchars($coordinador['regional_nombre']) ?></p>
                    <p class="text-gray-600">Centro: <?= htmlspecialchars($coordinador['centro_nombre']) ?></p>
                    <div class="mt-4 flex space-x-2">
                        <a href="editar_coordinador.php?id=<?= $coordinador['id'] ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button onclick="confirmarEliminacion(<?= $coordinador['id'] ?>)" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    // Función para cargar centros según la regional seleccionada
    function cargarCentrosPorRegional(regionalId) {
        const centroContainer = document.getElementById('centro-container');
        const centroSelect = document.getElementById('centro_id');
        const centroMensaje = document.getElementById('centro-mensaje');
        const submitBtn = document.getElementById('submit-btn');

        // Resetear estado
        centroMensaje.classList.add('hidden');
        centroMensaje.textContent = '';
        centroSelect.innerHTML = '<option value="">Cargando centros...</option>';
        centroSelect.disabled = true;
        submitBtn.disabled = true;
        
        if (!regionalId) {
            centroContainer.classList.add('hidden');
            return;
        }
        
        centroContainer.classList.remove('hidden');
        
        fetch(`../../controllers/SuperAdminController.php?action=obtener_centros_por_regional&regional_id=${regionalId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data.success && data.centros?.length > 0) {
                    let options = '<option value="">Seleccione un centro</option>';
                    data.centros.forEach(centro => {
                        options += `<option value="${centro.id}">${centro.nombre}</option>`;
                    });
                    centroSelect.innerHTML = options;
                    centroSelect.disabled = false;
                    
                    centroSelect.addEventListener('change', function() {
                        submitBtn.disabled = this.value === "";
                    });
                } else {
                    centroSelect.innerHTML = '<option value="" disabled>No hay centros disponibles</option>';
                    centroSelect.disabled = true;
                    centroMensaje.textContent = 'Esta regional no tiene centros disponibles';
                    centroMensaje.classList.remove('hidden');
                    submitBtn.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                centroSelect.innerHTML = '<option value="" disabled>Error al cargar centros</option>';
                centroSelect.disabled = true;
                centroMensaje.textContent = 'Error al cargar los centros. Por favor intente nuevamente.';
                centroMensaje.classList.remove('hidden');
                submitBtn.disabled = true;
            });
    }

    function openModal() {
        document.getElementById('modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('centro-container').classList.add('hidden');
        document.getElementById('submit-btn').disabled = true;
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('formCoordinador').reset();
        document.getElementById('centro-container').classList.add('hidden');
        document.getElementById('submit-btn').disabled = true;
    }

    function confirmarEliminacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este coordinador?')) {
            window.location.href = `../../controllers/SuperAdminController.php?action=delete_coordinador&id=${id}`;
        }
    }
    </script>

    <?php include '../partials/footer.php'; ?>
</body>
</html>