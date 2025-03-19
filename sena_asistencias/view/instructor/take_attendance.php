<?php
// Incluir la conexión a la base de datos
require_once '../../config/database.php'; // Asegúrate de que esta ruta es correcta

// Obtener la conexión
$db = Database::getInstance()->getConnection();

// Obtener programas de formación
$programas = $db->query("SELECT id, nombre FROM programas_formacion")->fetchAll(PDO::FETCH_ASSOC);

// Obtener fichas
$fichas = $db->query("SELECT id, codigo FROM fichas")->fetchAll(PDO::FETCH_ASSOC);

// Obtener ambientes
$ambientes = $db->query("SELECT id, nombre FROM ambientes")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Estilos y scripts -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Tomar Asistencia</h1>

    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-md mx-auto">
        <form action="../../controllers/InstructorController.php?action=take_attendance" method="POST" class="space-y-4">

            <!-- Programa de Formación -->
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
            <div>
                <label for="ficha_id" class="block text-sm font-medium text-gray-700 text-center">Ficha</label>
                <select name="ficha_id" id="ficha_id" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required onchange="cargarEstudiantes(this.value)">
                    <option value="">Seleccione una ficha</option>
                    <?php foreach ($fichas as $ficha): ?>
                        <option value="<?php echo $ficha['id']; ?>"><?php echo $ficha['codigo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ambiente -->
            <div>
                <label for="ambiente_id" class="block text-sm font-medium text-gray-700 text-center">Ambiente</label>
                <select name="ambiente_id" id="ambiente_id" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
                    <option value="">Seleccione un ambiente</option>
                    <?php foreach ($ambientes as $ambiente): ?>
                        <option value="<?php echo $ambiente['id']; ?>"><?php echo $ambiente['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700 text-center">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <div>
                <label for="hora_inicio" class="block text-sm font-medium text-gray-700 text-center">Hora de Inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <div>
                <label for="hora_fin" class="block text-sm font-medium text-gray-700 text-center">Hora de Fin</label>
                <input type="time" name="hora_fin" id="hora_fin" class="mt-1 block w-full max-w-xs px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mx-auto" required>
            </div>

            <!-- Lista de estudiantes -->
            <div id="lista-estudiantes" class="mt-4"></div>

        </form>
    </div>
</div>
<!-- Caja adicional para mostrar estudiantes y asistencia -->
<div id="asistencia-container" class="bg-gray-100 p-4 rounded-lg shadow-md border border-gray-300 hidden mt-4">
    <h2 class="text-xl font-bold text-center mb-3">Lista de Aprendices</h2>

    <table class="w-full border-collapse border border-gray-300 text-sm">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 p-2">Nombre</th>
                <th class="border border-gray-300 p-2">Fecha Inicio</th>
                <th class="border border-gray-300 p-2">Hora Inicio</th>
                <th class="border border-gray-300 p-2">Fecha Fin</th>
                <th class="border border-gray-300 p-2">Ambiente</th>
                <th class="border border-gray-300 p-2">Asistencia</th>
            </tr>
        </thead>
        <tbody id="lista-estudiantes">
            <!-- Se llenará con JavaScript -->
        </tbody>
    </table>
</div>

<script>
    function cargarEstudiantes(fichaId) {
        if (fichaId) {
            fetch(`../../controllers/InstructorController.php?action=get_estudiantes&ficha_id=${fichaId}`)
                .then(response => response.json())
                .then(data => {
                    const listaEstudiantes = document.getElementById('lista-estudiantes');
                    const asistenciaContainer = document.getElementById('asistencia-container');
                    listaEstudiantes.innerHTML = '';

                    if (data.length > 0) {
                        asistenciaContainer.classList.remove('hidden');

                        data.forEach(estudiante => {
                            const tr = document.createElement('tr');

                            tr.innerHTML = `
                                <td class="border border-gray-300 p-2 text-center">${estudiante.nombre} ${estudiante.apellido}</td>
                                <td class="border border-gray-300 p-2 text-center">${estudiante.fecha_inicio || 'N/A'}</td>
                                <td class="border border-gray-300 p-2 text-center">${estudiante.hora_inicio || 'N/A'}</td>
                                <td class="border border-gray-300 p-2 text-center">${estudiante.fecha_fin || 'N/A'}</td>
                                <td class="border border-gray-300 p-2 text-center">${document.getElementById('ambiente_id').selectedOptions[0].text}</td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="asistencia[${estudiante.id}]" class="px-2 py-1 border rounded-md">
                                        <option value="presente">Presente</option>
                                        <option value="ausente">Ausente</option>
                                        <option value="excusa">Excusa</option>
                                    </select>
                                </td>
                            `;

                            listaEstudiantes.appendChild(tr);
                        });
                    } else {
                        asistenciaContainer.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los estudiantes:', error);
                });
        } else {
            document.getElementById('asistencia-container').classList.add('hidden');
        }
    }

    function actualizarDatosAsistencia() {
        let fecha = document.getElementById('fecha').value;
        let horaInicio = document.getElementById('hora_inicio').value;
        let horaFin = document.getElementById('hora_fin').value;
        let ambiente = document.getElementById('ambiente_id').selectedOptions[0].text;

        document.getElementById('asistencia-fecha').innerText = fecha || 'N/A';
        document.getElementById('asistencia-hora-inicio').innerText = horaInicio || 'N/A';
        document.getElementById('asistencia-hora-fin').innerText = horaFin || 'N/A';
        document.getElementById('asistencia-ambiente').innerText = ambiente || 'N/A';
    }

    // Llamar la función cada vez que se actualicen los campos
    document.getElementById('fecha').addEventListener('change', actualizarDatosAsistencia);
    document.getElementById('hora_inicio').addEventListener('change', actualizarDatosAsistencia);
    document.getElementById('hora_fin').addEventListener('change', actualizarDatosAsistencia);
    document.getElementById('ambiente_id').addEventListener('change', actualizarDatosAsistencia);
</script>


<?php include '../partials/footer.php'; ?>