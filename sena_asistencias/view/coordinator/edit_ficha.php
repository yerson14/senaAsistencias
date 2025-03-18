<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'coordinador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../../models/ProgramaFormacionModel.php';
require_once '../../models/FichaModel.php';

// Obtener la conexión a la base de datos
$db = Database::getInstance()->getConnection();

// Crear instancias de los modelos
$programaFormacionModel = new ProgramaFormacionModel($db);
$fichaModel = new FichaModel($db);

// Obtener el ID de la ficha desde la URL
$id = $_GET['id'];

// Obtener la ficha por su ID
$ficha = $fichaModel->obtenerFichaPorId($id);

// Obtener los programas de formación
$programas = $programaFormacionModel->obtenerProgramas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ficha</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Editar Ficha</h1>

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

        <!-- Formulario para editar una ficha -->
        <form action="../../controllers/CoordinatorController.php?action=edit_ficha&id=<?php echo $id; ?>" method="POST">
            <div class="mb-4">
                <label for="numero" class="block text-sm font-medium text-gray-700">Número de Ficha</label>
                <input type="text" name="numero" id="numero" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="<?php echo $ficha['codigo']; ?>" required>
            </div>
            <div class="mb-4">
                <label for="programa_id" class="block text-sm font-medium text-gray-700">Programa</label>
                <select name="programa_id" id="programa_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione un Programa</option>
                    <?php foreach ($programas as $programa): ?>
                        <option value="<?php echo $programa['id']; ?>" <?php echo $programa['id'] == $ficha['programa_formacion_id'] ? 'selected' : ''; ?>>
                            <?php echo $programa['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="window.location.href='create_ficha.php'" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>