<?php
require_once '../../config/Database.php';
require_once '../../models/CentroModel.php';

// Obtener el ID del centro desde la URL
$centro_id = $_GET['id'];

// Obtener los detalles del centro desde la base de datos
$centroModel = new CentroModel(Database::getInstance()->getConnection());
$centro = $centroModel->obtenerCentroPorId($centro_id);

if (!$centro) {
    $_SESSION['error'] = "Centro no encontrado.";
    header("Location: ../views/superadmin/crear_centro.php");
    exit();
}
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Centro Creado</h1>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Detalles del Centro</h2>
        <p><strong>Nombre:</strong> <?php echo $centro['nombre']; ?></p>
        <p><strong>Regional:</strong> <?php echo $centro['regional_nombre']; ?></p>
    </div>
    <a href="crear_centro.php" class="mt-4 inline-block bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
        Crear Otro Centro
    </a>
</div>

<?php include '../partials/footer.php'; ?>