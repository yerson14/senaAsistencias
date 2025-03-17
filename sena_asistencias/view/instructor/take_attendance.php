<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Tomar Asistencia</h1>
    <form action="../../controllers/InstructorController.php?action=take_attendance" method="POST" class="bg-white p-6 rounded-lg shadow-md">
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="ficha_id" class="block text-sm font-medium text-gray-700">Ficha</label>
                <select name="ficha_id" id="ficha_id" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <!-- Opciones de fichas -->
                </select>
            </div>
            <div>
                <label for="ambiente_id" class="block text-sm font-medium text-gray-700">Ambiente</label>
                <select name="ambiente_id" id="ambiente_id" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <!-- Opciones de ambientes -->
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="programa_formacion" class="block text-sm font-medium text-gray-700">Programa de Formación</label>
                <input type="text" name="programa_formacion" id="programa_formacion" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="hora_inicio" class="block text-sm font-medium text-gray-700">Hora de Inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label for="hora_fin" class="block text-sm font-medium text-gray-700">Hora de Fin</label>
                <input type="time" name="hora_fin" id="hora_fin" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="estado" id="estado" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="presente">Presente</option>
                <option value="ausente">Ausente</option>
            </select>
        </div>
        
        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">
            Tomar Asistencia
        </button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>