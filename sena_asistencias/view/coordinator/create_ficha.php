<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'coordinador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/ProgramaFormacionModel.php';
require_once '../../models/FichaModel.php';

// Obtener los programas de formación con información de centro y regional
$programaFormacionModel = new ProgramaFormacionModel(Database::getInstance()->getConnection());
$programas = $programaFormacionModel->obtenerProgramasConRegionalYCentro();

// Obtener las fichas con información completa
$fichaModel = new FichaModel(Database::getInstance()->getConnection());
$fichas = $fichaModel->obtenerFichasCompletas();
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
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensajes de éxito -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Botón flotante para agregar ficha -->
        <button onclick="openModal()" class="fixed bottom-8 right-8 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition duration-300 z-50">
            <i class="fas fa-plus text-2xl"></i>
        </button>

        <!-- Modal para agregar ficha -->
        <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Agregar Ficha</h2>
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
                                <option value="<?php echo $programa['id']; ?>" data-regional="<?php echo $programa['regional_nombre']; ?>" data-centro="<?php echo $programa['centro_nombre']; ?>">
                                    <?php echo $programa['nombre']; ?> (<?php echo $programa['centro_nombre']; ?> - <?php echo $programa['regional_nombre']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4 bg-gray-100 p-3 rounded-md hidden" id="programa-info">
                        <p class="text-sm"><strong>Regional:</strong> <span id="info-regional"></span></p>
                        <p class="text-sm"><strong>Centro:</strong> <span id="info-centro"></span></p>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de fichas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($fichas as $ficha): ?>
                <div class="border-2 border-green-500 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2">Ficha #<?php echo $ficha['codigo']; ?></h2>
                    <p class="text-gray-600">Programa: <?php echo $ficha['programa_nombre']; ?></p>
                    <p class="text-gray-600">Centro: <?php echo $ficha['centro_nombre']; ?></p>
                    <p class="text-gray-600">Regional: <?php echo $ficha['regional_nombre']; ?></p>
                    <div class="mt-4 flex space-x-2">
                        <a href="edit_ficha.php?id=<?php echo $ficha['id']; ?>"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 ease-in-out flex items-center space-x-2 shadow hover:shadow-md">
                            <i class="fas fa-edit"></i>
                            <span>Editar</span>
                        </a>
                        <a href="../../controllers/CoordinatorController.php?action=delete_ficha&id=<?php echo $ficha['id']; ?>"
                            class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 ease-in-out flex items-center space-x-2 shadow hover:shadow-md"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta ficha?');">
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

        // Mostrar información del programa seleccionado
        document.getElementById('programa_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const infoDiv = document.getElementById('programa-info');
            
            if (selectedOption.value) {
                document.getElementById('info-regional').textContent = selectedOption.dataset.regional;
                document.getElementById('info-centro').textContent = selectedOption.dataset.centro;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        });
    </script>

    <?php include '../partials/footer.php'; ?>
</body>

</html>