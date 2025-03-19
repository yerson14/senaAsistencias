<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">




<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>



<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Tomar Asistencia</h1> <!-- Título centrado -->

    <!-- Contenedor del cuadro con ancho reducido y centrado -->
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-md mx-auto">
        <form action="../../controllers/InstructorController.php?action=take_attendance" method="POST" class="space-y-4">

        <div>
    <label for="programa_formacion" class="block text-sm font-medium text-gray-700 text-center">Programa de Formación</label>
    <select name="programa_formacion" id="programa_formacion" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
    <option value="">Seleccione un programa</option>
    <?php foreach ($programas as $programa): ?>
        <option value="<?php echo $programa['id']; ?>"><?php echo $programa['nombre']; ?></option>
    <?php endforeach; ?>
</select>
</div>

            <!-- Ficha -->
            <!-- Ficha -->
            <div>
                <label for="ficha_id" class="block text-sm font-medium text-gray-700 text-center">Ficha</label>
                <select name="ficha_id" id="ficha_id" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required onchange="cargarEstudiantes(this.value)">
                    <!-- Opciones de fichas -->
                    <option value="">Seleccione una ficha</option>
                    <!-- Aquí se cargarán las opciones de fichas dinámicamente -->
                </select>
            </div>

            <!-- Contenedor para la lista de estudiantes -->
            <div id="lista-estudiantes" class="mt-4">
                <!-- Aquí se cargarán los estudiantes -->
            </div>
            <!-- Ambiente -->
            <div>
                <label for="ambiente_id" class="block text-sm font-medium text-gray-700 text-center">Ambiente</label>
                <select name="ambiente_id" id="ambiente_id" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
                    <!-- Opciones de ambientes -->
                </select>
            </div>

            <!-- Programa de Formación -->
            <!-- Fecha -->
            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700 text-center">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <!-- Fecha de Inicio -->
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 text-center">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <!-- Fecha de Fin -->
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 text-center">Fecha de Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <!-- Hora de Inicio -->
            <div>
                <label for="hora_inicio" class="block text-sm font-medium text-gray-700 text-center">Hora de Inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <!-- Hora de Fin -->
            <div>
                <label for="hora_fin" class="block text-sm font-medium text-gray-700 text-center">Hora de Fin</label>
                <input type="time" name="hora_fin" id="hora_fin" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <!-- Estado (Reemplazado por opciones de radio) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 text-center">Estado</label>
                <div class="mt-1 flex justify-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="estado" value="presente" class="form-radio h-4 w-4 text-blue-600" required>
                        <span class="ml-2 text-gray-700">Presente</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="estado" value="ausente" class="form-radio h-4 w-4 text-blue-600" required>
                        <span class="ml-2 text-gray-700">Ausente</span>
                    </label>
                </div>
            </div>

            <!-- Botón de enviar -->
            <button type="submit" class="w-full max-w-xs bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300 mx-auto block">
                Tomar Asistencia
            </button>
        </form>
    </div>
</div>
<script>
    function cargarEstudiantes(fichaId) {
        if (fichaId) {
            // Realiza una solicitud AJAX para obtener los estudiantes de la ficha seleccionada
            fetch(`../../controllers/InstructorController.php?action=get_estudiantes&ficha_id=${fichaId}`)
                .then(response => response.json())
                .then(data => {
                    const listaEstudiantes = document.getElementById('lista-estudiantes');
                    listaEstudiantes.innerHTML = ''; // Limpia la lista anterior

                    if (data.length > 0) {
                        const ul = document.createElement('ul');
                        ul.className = 'space-y-2';

                        data.forEach(estudiante => {
                            const li = document.createElement('li');
                            li.className = 'bg-gray-100 p-2 rounded-md';
                            li.textContent = `${estudiante.nombre} ${estudiante.apellido}`;
                            ul.appendChild(li);
                        });

                        listaEstudiantes.appendChild(ul);
                    } else {
                        listaEstudiantes.innerHTML = '<p class="text-center text-gray-500">No hay estudiantes en esta ficha.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los estudiantes:', error);
                });
        } else {
            // Si no se selecciona una ficha, limpia la lista de estudiantes
            document.getElementById('lista-estudiantes').innerHTML = '';
        }
    }
</script>
<?php include '../partials/footer.php'; ?>