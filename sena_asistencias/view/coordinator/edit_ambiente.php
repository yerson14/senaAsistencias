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

$id = $_GET['id'];
$ambiente = $ambienteModel->obtenerAmbienteCompleto($id);
$regionales = $regionalModel->obtenerRegionales();
$centrosRegional = $centroModel->obtenerCentrosPorRegional($ambiente['regional_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ambiente</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Editar Ambiente</h1>

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

        <form action="../../controllers/CoordinatorController.php?action=edit_ambiente&id=<?php echo $id; ?>" method="POST">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Ambiente</label>
                <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       value="<?php echo $ambiente['nombre']; ?>" required>
            </div>
            
            <div class="mb-4">
                <label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label>
                <select name="regional_id" id="regional_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required 
                        onchange="cargarCentros(this.value, <?php echo $ambiente['centro_id']; ?>)">
                    <option value="">Seleccione una regional</option>
                    <?php foreach ($regionales as $regional): ?>
                        <option value="<?php echo $regional['id']; ?>" <?php echo $regional['id'] == $ambiente['regional_id'] ? 'selected' : ''; ?>>
                            <?php echo $regional['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro</label>
                <select name="centro_id" id="centro_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione un centro</option>
                    <?php foreach ($centrosRegional as $centro): ?>
                        <option value="<?php echo $centro['id']; ?>" <?php echo $centro['id'] == $ambiente['centro_id'] ? 'selected' : ''; ?>>
                            <?php echo $centro['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="window.location.href='index.php'" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <script>
        function cargarCentros(regional_id, centroSeleccionado = null) {
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
                            var selected = (centroSeleccionado && centro.id == centroSeleccionado) ? 'selected' : '';
                            select.append('<option value="' + centro.id + '" ' + selected + '>' + centro.nombre + '</option>');
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