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

    <!-- Caja principal del formulario -->
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto mb-8">
        <form action="../../controllers/InstructorController.php?action=take_attendance" method="POST" class="space-y-4" id="form-asistencia">
            <div class="grid grid-cols-7 gap-4">
                <!-- Programa de Formación -->
                <div>
                    <label for="programa_formacion" class="block text-sm font-medium text-gray-700">Programa</label>
                    <select name="programa_formacion" id="programa_formacion" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($programas as $programa): ?>
                            <option value="<?php echo $programa['id']; ?>"><?php echo $programa['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ficha -->
                <div>
                    <label for="ficha_id" class="block text-sm font-medium text-gray-700">Ficha</label>
                    <select name="ficha_id" id="ficha_id" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($fichas as $ficha): ?>
                            <option value="<?php echo $ficha['id']; ?>"><?php echo $ficha['codigo']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ambiente -->
                <div>
                    <label for="ambiente_id" class="block text-sm font-medium text-gray-700">Ambiente</label>
                    <select name="ambiente_id" id="ambiente_id" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($ambientes as $ambiente): ?>
                            <option value="<?php echo $ambiente['id']; ?>"><?php echo $ambiente['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <!-- Hora de Inicio -->
                <div>
                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700">Hora Inicio</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <!-- Hora de Fin -->
                <div>
                    <label for="hora_fin" class="block text-sm font-medium text-gray-700">Hora Fin</label>
                    <input type="time" name="hora_fin" id="hora_fin" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <!-- Botón de guardar asistencia (ícono de chulo) -->
                <div class="flex items-end">
                    <button type="button" onclick="mostrarAprendices()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-check"></i> <!-- Ícono de chulo -->
                    </button>
                </div>
            </div>

            <!-- Campo oculto para enviar la asistencia -->
            <input type="hidden" name="asistencia" id="asistencia-data">
        </form>
    </div>

    <!-- Caja de la lista de aprendices -->
    <div id="asistencia-container" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-6xl mx-auto hidden">
        <h2 class="text-xl font-bold text-center mb-6">Lista de Aprendices</h2>

        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-2">Nombre</th>
                    <th class="border border-gray-300 p-2">Programa de Formación</th>
                    <th class="border border-gray-300 p-2">Ficha</th>
                    <th class="border border-gray-300 p-2">Fecha</th>
                    <th class="border border-gray-300 p-2">Hora Inicio</th>
                    <th class="border border-gray-300 p-2">Hora Fin</th>
                    <th class="border border-gray-300 p-2">Ambiente</th>
                    <th class="border border-gray-300 p-2">Asistencia</th>
                </tr>
            </thead>
            <tbody id="lista-aprendices">
                <!-- Se llenará con JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Contenedor para el historial de llamados a lista -->
    <div id="historial-container" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-6xl mx-auto mt-8">
        <h2 class="text-xl font-bold text-center mb-6">Historial de Llamados a Lista</h2>
        <!-- Aquí se mostrará el historial -->
    </div>
</div>

<script>
    // Objeto para almacenar la asistencia de cada aprendiz
    const asistenciaSeleccionada = {};

    // Array para almacenar el historial de llamados a lista del día actual
    let historialLlamados = [];

    // Función para mostrar la lista de aprendices
    function mostrarAprendices() {
        const fichaId = document.getElementById('ficha_id').value;
        const fecha = document.getElementById('fecha').value;

        if (fichaId && fecha) {
            cargarAprendices(fichaId, fecha);
        } else {
            alert("Por favor, seleccione una ficha y una fecha primero.");
        }
    }

    function cargarAprendices(fichaId, fecha) {
        if (fichaId && fecha) {
            fetch(`../../controllers/InstructorController.php?action=get_aprendices&ficha_id=${fichaId}`)
                .then(response => response.json())
                .then(data => {
                    const listaAprendices = document.getElementById('lista-aprendices');
                    const asistenciaContainer = document.getElementById('asistencia-container');
                    listaAprendices.innerHTML = '';

                    if (data.length > 0) {
                        asistenciaContainer.classList.remove('hidden');

                        // Obtener valores de los campos
                        const programa = document.getElementById('programa_formacion').selectedOptions[0].text || 'N/A';
                        const ficha = document.getElementById('ficha_id').selectedOptions[0].text || 'N/A';
                        const horaInicio = document.getElementById('hora_inicio').value || 'N/A';
                        const horaFin = document.getElementById('hora_fin').value || 'N/A';
                        const ambiente = document.getElementById('ambiente_id').selectedOptions[0].text || 'N/A';

                        // Guardar el llamado a lista en el historial
                        const llamado = {
                            programa,
                            ficha,
                            fecha,
                            horaInicio,
                            horaFin,
                            ambiente,
                            aprendices: data
                        };

                        // Verificar si ya existe un llamado a lista en la misma fecha
                        const existeLlamado = historialLlamados.some(llamado => llamado.fecha === fecha);

                        if (!existeLlamado) {
                            historialLlamados = [llamado]; // Reiniciar el historial si es un nuevo día
                        } else {
                            historialLlamados.push(llamado); // Agregar al historial si es el mismo día
                        }

                        // Mostrar el historial
                        mostrarHistorial();

                        data.forEach(aprendiz => {
                            const tr = document.createElement('tr');

                            tr.innerHTML = `
                                <td class="border border-gray-300 p-2 text-center">${aprendiz.nombre}</td>
                                <td class="border border-gray-300 p-2 text-center">${programa}</td>
                                <td class="border border-gray-300 p-2 text-center">${ficha}</td>
                                <td class="border border-gray-300 p-2 text-center">${fecha}</td>
                                <td class="border border-gray-300 p-2 text-center">${horaInicio}</td>
                                <td class="border border-gray-300 p-2 text-center">${horaFin}</td>
                                <td class="border border-gray-300 p-2 text-center">${ambiente}</td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <div class="flex space-x-2">
                                        <button type="button" data-aprendiz-id="${aprendiz.id}" data-estado="presente" onclick="marcarAsistencia(${aprendiz.id}, 'presente')" class="px-3 py-1 bg-gray-300 text-white rounded-md hover:bg-gray-400">Presente</button>
                                        <button type="button" data-aprendiz-id="${aprendiz.id}" data-estado="ausente" onclick="marcarAsistencia(${aprendiz.id}, 'ausente')" class="px-3 py-1 bg-gray-300 text-white rounded-md hover:bg-gray-400">Ausente</button>
                                        <button type="button" data-aprendiz-id="${aprendiz.id}" data-estado="excusa" onclick="marcarAsistencia(${aprendiz.id}, 'excusa')" class="px-3 py-1 bg-gray-300 text-white rounded-md hover:bg-gray-400">Excusa</button>
                                    </div>
                                </td>
                            `;

                            listaAprendices.appendChild(tr);
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

    // Función para mostrar el historial de llamados a lista
    function mostrarHistorial() {
        const historialContainer = document.getElementById('historial-container');
        historialContainer.innerHTML = '';

        if (historialLlamados.length > 0) {
            historialLlamados.forEach((llamado, index) => {
                const div = document.createElement('div');
                div.className = 'bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-6xl mx-auto mb-8';

                div.innerHTML = `
                    <h2 class="text-xl font-bold text-center mb-6">Llamado a lista #${index + 1}</h2>
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 p-2">Nombre</th>
                                <th class="border border-gray-300 p-2">Programa de Formación</th>
                                <th class="border border-gray-300 p-2">Ficha</th>
                                <th class="border border-gray-300 p-2">Fecha</th>
                                <th class="border border-gray-300 p-2">Hora Inicio</th>
                                <th class="border border-gray-300 p-2">Hora Fin</th>
                                <th class="border border-gray-300 p-2">Ambiente</th>
                                <th class="border border-gray-300 p-2">Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${llamado.aprendices.map(aprendiz => `
                                <tr>
                                    <td class="border border-gray-300 p-2 text-center">${aprendiz.nombre}</td>
                                    <td class="border border-gray-300 p-2 text-center">${llamado.programa}</td>
                                    <td class="border border-gray-300 p-2 text-center">${llamado.ficha}</td>
                                    <td class="border border-gray-300 p-2 text-center">${llamado.fecha}</td>
                                    <td class="border border-gray-300 p-2 text-center">${llamado.horaInicio}</td>
                                    <td class="border border-gray-300 p-2 text-center">${llamado.horaFin}</td>
                                    <td class="border border-gray-300 p-2 text-center">${llamado.ambiente}</td>
                                    <td class="border border-gray-300 p-2 text-center">
                                        ${asistenciaSeleccionada[aprendiz.id] || 'Sin registrar'}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;

                historialContainer.appendChild(div);
            });
        }
    }

    // Función para marcar la asistencia de un aprendiz
    function marcarAsistencia(aprendizId, estado) {
        // Almacenar la selección en el objeto
        asistenciaSeleccionada[aprendizId] = estado;

        // Cambiar el estilo del botón seleccionado
        const botones = document.querySelectorAll(`button[data-aprendiz-id="${aprendizId}"]`);
        botones.forEach(boton => {
            boton.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500', 'hover:bg-green-600', 'hover:bg-red-600', 'hover:bg-yellow-600');
            boton.classList.add('bg-gray-300', 'hover:bg-gray-400');
        });

        // Resaltar el botón seleccionado
        const botonSeleccionado = document.querySelector(`button[data-aprendiz-id="${aprendizId}"][data-estado="${estado}"]`);
        if (botonSeleccionado) {
            if (estado === 'presente') {
                botonSeleccionado.classList.remove('bg-gray-300', 'hover:bg-gray-400');
                botonSeleccionado.classList.add('bg-green-500', 'hover:bg-green-600');
            } else if (estado === 'ausente') {
                botonSeleccionado.classList.remove('bg-gray-300', 'hover:bg-gray-400');
                botonSeleccionado.classList.add('bg-red-500', 'hover:bg-red-600');
            } else if (estado === 'excusa') {
                botonSeleccionado.classList.remove('bg-gray-300', 'hover:bg-gray-400');
                botonSeleccionado.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
            }
        }

        console.log(`Asistencia del aprendiz ${aprendizId}: ${estado}`);
    }

    // Enviar la asistencia seleccionada al formulario
    document.getElementById('form-asistencia').addEventListener('submit', function (e) {
        e.preventDefault();
        const asistenciaData = document.getElementById('asistencia-data');
        asistenciaData.value = JSON.stringify(asistenciaSeleccionada);
        this.submit();
    });
</script>

<?php include '../partials/footer.php'; ?>