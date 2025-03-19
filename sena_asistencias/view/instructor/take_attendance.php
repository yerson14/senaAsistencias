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

// Manejo de guardado de asistencia
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_asistencia'])) {
    $asistenciaData = json_decode($_POST['asistencia_data'], true);
    $fichaId = $_POST['ficha_id'];
    $programaId = $_POST['programa_formacion'];
    $ambienteId = $_POST['ambiente_id'];
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFin = $_POST['hora_fin'];
    
    if (!empty($asistenciaData)) {
        try {
            // Iniciar transacción para asegurar que todos los registros se guarden
            $db->beginTransaction();
            
            foreach ($asistenciaData as $aprendizId => $estado) {
                $stmt = $db->prepare("INSERT INTO asistencias (aprendiz_id, ficha_id, programa_id, ambiente_id, fecha, hora_inicio, hora_fin, estado) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$aprendizId, $fichaId, $programaId, $ambienteId, $fecha, $horaInicio, $horaFin, $estado]);
            }
            
            // Confirmar la transacción
            $db->commit();
            $mensaje = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                         <strong>¡Éxito!</strong> Asistencia guardada correctamente.
                       </div>';
        } catch (Exception $e) {
            // Revertir en caso de error
            $db->rollBack();
            $mensaje = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                         <strong>Error:</strong> No se pudo guardar la asistencia. ' . $e->getMessage() . '
                       </div>';
        }
    } else {
        $mensaje = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4" role="alert">
                     <strong>Atención:</strong> No hay datos de asistencia para guardar.
                   </div>';
    }
}

// Función para obtener los aprendices por ficha
function obtenerAprendicesPorFicha($db, $fichaId) {
    $stmt = $db->prepare("SELECT id, nombre FROM aprendices WHERE ficha_id = ?");
    $stmt->execute([$fichaId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- Estilos y scripts -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Tomar Asistencia</h1>

    <?php echo $mensaje; ?>

    <!-- Caja principal del formulario -->
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto mb-8">
        <form id="form-asistencia" method="POST" class="space-y-4">
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

                <!-- Botón de mostrar aprendices (ícono de chulo) -->
                <div class="flex items-end">
                    <button type="button" id="btn-mostrar-aprendices" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-check"></i> <!-- Ícono de chulo -->
                    </button>
                </div>
            </div>

            <!-- Campo oculto para enviar la asistencia -->
            <input type="hidden" name="asistencia_data" id="asistencia-data">
            
            <!-- Caja de la lista de aprendices -->
            <div id="asistencia-container" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto hidden mt-6">
                <h2 class="text-xl font-bold text-center mb-6">Lista de Aprendices</h2>
                
                <div id="loading-message" class="text-center p-4 hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando aprendices...
                </div>
                
                <div id="no-data-message" class="text-center p-4 text-red-500 hidden">
                    No se encontraron aprendices para esta ficha.
                </div>
                
                <table class="w-full border-collapse border border-gray-300 text-sm" id="tabla-aprendices">
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
                
                <!-- Botón para guardar la asistencia -->
                <div class="mt-6 text-center">
                    <button type="submit" name="guardar_asistencia" class="px-6 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i> Guardar Asistencia
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Objeto para almacenar la asistencia de cada aprendiz
    const asistenciaSeleccionada = {};
    
    // Función para mostrar/ocultar elementos
    function toggleElement(id, mostrar) {
        const elemento = document.getElementById(id);
        if (mostrar) {
            elemento.classList.remove('hidden');
        } else {
            elemento.classList.add('hidden');
        }
    }

    // Evento para mostrar aprendices al hacer clic en el botón
    document.getElementById('btn-mostrar-aprendices').addEventListener('click', function() {
        const fichaId = document.getElementById('ficha_id').value;
        const programaId = document.getElementById('programa_formacion').value;
        const ambienteId = document.getElementById('ambiente_id').value;
        const fecha = document.getElementById('fecha').value;
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaFin = document.getElementById('hora_fin').value;
        
        // Validar campos obligatorios
        if (!fichaId || !programaId || !ambienteId || !fecha || !horaInicio || !horaFin) {
            alert("Por favor, complete todos los campos requeridos.");
            return;
        }
        
        cargarAprendices();
    });

    // Función para cargar aprendices
    function cargarAprendices() {
        const fichaId = document.getElementById('ficha_id').value;
        if (!fichaId) return;
        
        // Mostrar el contenedor de asistencia y mensaje de carga
        toggleElement('asistencia-container', true);
        toggleElement('loading-message', true);
        toggleElement('no-data-message', false);
        toggleElement('tabla-aprendices', false);
        
        // Limpiar tabla anterior
        document.getElementById('lista-aprendices').innerHTML = '';
        
        // Obtener valores de los campos para mostrar en la tabla
        const programa = document.getElementById('programa_formacion').selectedOptions[0].text || 'N/A';
        const ficha = document.getElementById('ficha_id').selectedOptions[0].text || 'N/A';
        const fecha = document.getElementById('fecha').value || 'N/A';
        const horaInicio = document.getElementById('hora_inicio').value || 'N/A';
        const horaFin = document.getElementById('hora_fin').value || 'N/A';
        const ambiente = document.getElementById('ambiente_id').selectedOptions[0].text || 'N/A';
        
        // Realizar la solicitud AJAX para obtener los aprendices
        fetch(obtener_aprendices.php?ficha_id=${fichaId}) // Ajustar esta URL según corresponda
            .then(response => response.json())
            .then(data => {
                toggleElement('loading-message', false);
                
                if (data.length > 0) {
                    toggleElement('tabla-aprendices', true);
                    
                    const listaAprendices = document.getElementById('lista-aprendices');
                    
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
                                <div class="flex space-x-2 justify-center">
                                    <button type="button" data-aprendiz-id="${aprendiz.id}" data-estado="presente" 
                                        class="asistencia-btn px-3 py-1 bg-gray-300 text-white rounded-md hover:bg-gray-400">
                                        Presente
                                    </button>
                                    <button type="button" data-aprendiz-id="${aprendiz.id}" data-estado="ausente" 
                                        class="asistencia-btn px-3 py-1 bg-gray-300 text-white rounded-md hover:bg-gray-400">
                                        Ausente
                                    </button>
                                    <button type="button" data-aprendiz-id="${aprendiz.id}" data-estado="excusa" 
                                        class="asistencia-btn px-3 py-1 bg-gray-300 text-white rounded-md hover:bg-gray-400">
                                        Excusa
                                    </button>
                                </div>
                            </td>
                        `;
                        
                        listaAprendices.appendChild(tr);
                    });
                    
                    // Agregar eventos a todos los botones de asistencia
                    document.querySelectorAll('.asistencia-btn').forEach(boton => {
                        boton.addEventListener('click', function() {
                            const aprendizId = this.getAttribute('data-aprendiz-id');
                            const estado = this.getAttribute('data-estado');
                            marcarAsistencia(aprendizId, estado);
                        });
                    });
                } else {
                    toggleElement('no-data-message', true);
                }
            })
            .catch(error => {
                console.error('Error al cargar los aprendices:', error);
                toggleElement('loading-message', false);
                toggleElement('no-data-message', true);
                document.getElementById('no-data-message').innerText = 'Error al cargar los aprendices. Intente nuevamente.';
            });
    }

    // Función para marcar la asistencia de un aprendiz
    function marcarAsistencia(aprendizId, estado) {
        // Almacenar la selección en el objeto
        asistenciaSeleccionada[aprendizId] = estado;
        
        // Actualizar el campo oculto con los datos de asistencia
        document.getElementById('asistencia-data').value = JSON.stringify(asistenciaSeleccionada);
        
        // Cambiar el estilo de los botones para ese aprendiz
        const botones = document.querySelectorAll(button[data-aprendiz-id="${aprendizId}"]);
        botones.forEach(boton => {
            // Restablecer todos los botones a gris
            boton.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500', 'hover:bg-green-600', 'hover:bg-red-600', 'hover:bg-yellow-600');
            boton.classList.add('bg-gray-300', 'hover:bg-gray-400');
        });
        
        // Resaltar el botón seleccionado con el color correspondiente
        const botonSeleccionado = document.querySelector(button[data-aprendiz-id="${aprendizId}"][data-estado="${estado}"]);
        if (botonSeleccionado) {
            botonSeleccionado.classList.remove('bg-gray-300', 'hover:bg-gray-400');
            
            if (estado === 'presente') {
                botonSeleccionado.classList.add('bg-green-500', 'hover:bg-green-600');
            } else if (estado === 'ausente') {
                botonSeleccionado.classList.add('bg-red-500', 'hover:bg-red-600');
            } else if (estado === 'excusa') {
                botonSeleccionado.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
            }
        }
    }

    // Verificar si hay un mensaje después de enviar el formulario y scrollear a él
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($mensaje)): ?>
        const mensajeElement = document.querySelector('.bg-green-100, .bg-red-100, .bg-yellow-100');
        if (mensajeElement) {
            mensajeElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Auto-ocultar el mensaje después de 5 segundos
            setTimeout(function() {
                mensajeElement.style.transition = 'opacity 1s ease-out';
                mensajeElement.style.opacity = '0';
                setTimeout(function() {
                    mensajeElement.style.display = 'none';
                }, 1000);
            }, 5000);
        }
        <?php endif; ?>
    });

    // Asegurarse de que el campo oculto se llene antes de enviar el formulario
    document.getElementById('form-asistencia').addEventListener('submit', function(event) {
        // Convertir el objeto asistenciaSeleccionada a JSON y asignarlo al campo oculto
        document.getElementById('asistencia-data').value = JSON.stringify(asistenciaSeleccionada);
        
        // Validar que haya datos de asistencia para guardar
        if (Object.keys(asistenciaSeleccionada).length === 0) {
            event.preventDefault(); // Evitar el envío del formulario
            alert("Por favor, marque la asistencia de al menos un aprendiz.");
        }
    });
</script>

<?php
// Este código PHP se ejecuta al final del archivo para obtener los aprendices por ficha si se solicita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['obtener_aprendices'])) {
    $fichaId = $_POST['obtener_aprendices'];
    $aprendices = obtenerAprendicesPorFicha($db, $fichaId);
    
    // Generar la respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($aprendices);
    exit;
}
?>

<?php include '../partials/footer.php'; ?>