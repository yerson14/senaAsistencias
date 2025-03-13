<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Crear Regional</h1>
    <form action="../../controllers/SuperAdminController.php?action=create_regional" method="POST">
        <div class="mb-4">
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Regional</label>
            <input type="text" name="nombre" id="nombre" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
            Crear Regional
        </button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>