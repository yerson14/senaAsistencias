<?php
session_start();
require_once '../../config/Database.php'; // Asegúrate de que la ruta sea correcta
require_once '../../models/RegionalModel.php'; // Asegúrate de que la ruta sea correcta

// Obtener las regionales desde la base de datos
$regionalModel = new RegionalModel(Database::getInstance()->getConnection());
$regionales = $regionalModel->obtenerRegionales();
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Crear Centro</h1>
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
    <form action="../../controllers/SuperAdminController.php?action=create_center" method="POST">
        <div class="mb-4">
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Centro</label>
            <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div class="mb-4">
            <label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label>
            <select name="regional_id" id="regional_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Seleccione una regional</option>
                <?php foreach ($regionales as $regional): ?>
                    <option value="<?php echo $regional['id']; ?>"><?php echo $regional['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
            Crear Centro
        </button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>