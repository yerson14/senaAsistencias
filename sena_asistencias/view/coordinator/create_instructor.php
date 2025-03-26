<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'coordinador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/InstructorModel.php';
require_once '../../models/CentroModel.php';

$db = Database::getInstance()->getConnection();
$instructorModel = new InstructorModel($db);
$centroModel = new CentroModel($db);

$instructores = $instructorModel->obtenerInstructores();
$centros = $centroModel->obtenerCentros();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Instructores</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Gestión de Instructores</h1>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Botón flotante para agregar instructor -->
        <button onclick="openModal()" class="fixed bottom-8 right-8 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition duration-300 z-50">
            <i class="fas fa-plus text-2xl"></i>
        </button>

        <!-- Modal para agregar instructor -->
        <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Agregar Instructor</h2>
                <form action="../../controllers/CoordinatorController.php?action=create_instructor" method="POST">
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="correo" class="block text-sm font-medium text-gray-700">Correo</label>
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

        <!-- Lista de instructores -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($instructores as $instructor): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2"><?php echo $instructor['nombre']; ?></h2>
                    <p class="text-gray-600">Correo: <?php echo $instructor['correo']; ?></p>
                    <p class="text-gray-600">Número de Identificación: <?php echo $instructor['numero_identificacion']; ?></p>
                    <p class="text-gray-600">Centro: <?php echo $instructor['centro_nombre']; ?></p>
                    <div class="mt-4 flex space-x-2">
                        <a href="edit_instructor.php?id=<?php echo $instructor['id']; ?>"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 ease-in-out flex items-center space-x-2 shadow hover:shadow-md">
                            <i class="fas fa-edit"></i>
                            <span>Editar</span>
                        </a>
                        <a href="../../controllers/CoordinatorController.php?action=delete_instructor&id=<?php echo $instructor['id']; ?>"
                            class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 ease-in-out flex items-center space-x-2 shadow hover:shadow-md"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar este instructor?');">
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
    </script>

    <?php include '../partials/footer.php'; ?>
</body>

</html>