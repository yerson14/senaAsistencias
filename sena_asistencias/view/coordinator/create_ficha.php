<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Crear Ficha</h1>
    <form action="../../controllers/CoordinadorController.php?action=create_ficha" method="POST">
        <div class="mb-4">
            <label for="codigo" class="block text-sm font-medium text-gray-700">Código de la Ficha</label>
            <input type="text" name="codigo" id="codigo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div class="mb-4">
            <label for="programa_formacion_id" class="block text-sm font-medium text-gray-700">Programa de Formación</label>
            <select name="programa_formacion_id" id="programa_formacion_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                <!-- Opciones de programas de formación -->
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
            Crear Ficha
        </button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>