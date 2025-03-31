<?php
// Incluir la conexión a la base de datos
require_once '../../config/database.php';

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
$mostrarTablaAprendices = false;
$aprendices = [];
$asistenciaData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar_asistencia'])) {
        $asistenciaData = isset($_POST['asistencia_data']) ? json_decode($_POST['asistencia_data'], true) : [];
        $fichaId = $_POST['ficha_id'];
        $programaId = $_POST['programa_formacion'];
        $ambienteId = $_POST['ambiente_id'];
        $fecha = $_POST['fecha'];
        $horaInicio = $_POST['hora_inicio'];
        $horaFin = $_POST['hora_fin'];

        if (!empty($asistenciaData)) {
            try {
                $db->beginTransaction();

                foreach ($asistenciaData as $aprendizId => $estado) {
                    $stmt = $db->prepare("INSERT INTO asistencias (aprendiz_id, ficha_id, programa_id, ambiente_id, fecha, hora_inicio, hora_fin, estado) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$aprendizId, $fichaId, $programaId, $ambienteId, $fecha, $horaInicio, $horaFin, $estado]);

                    if ($estado === 'ausente') {
                        $stmtCount = $db->prepare("SELECT COUNT(*) as total_ausentes FROM asistencias WHERE aprendiz_id = ? AND estado = 'ausente'");
                        $stmtCount->execute([$aprendizId]);
                        $result = $stmtCount->fetch(PDO::FETCH_ASSOC);
                        $totalAusentes = $result['total_ausentes'];

                        if ($totalAusentes >= 3) {
                            session_start();
                            $_SESSION['aprendiz_id'] = $aprendizId;
                        }
                    }
                }

                $db->commit();
                $mensaje = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                             <strong>¡Éxito!</strong> Asistencia guardada correctamente.
                           </div>';
                $mostrarTablaAprendices = true;

                if (isset($_SESSION['aprendiz_id'])) {
                    header('Location: view_reports.php');
                    exit;
                }
            } catch (Exception $e) {
                $db->rollBack();
                $mensaje = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                             <strong>Error:</strong> No se pudo guardar la asistencia. ' . $e->getMessage() . '
                           </div>';
                $mostrarTablaAprendices = true;
            }
        } else {
            $mensaje = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4" role="alert">
                         <strong>Atención:</strong> No hay datos de asistencia para guardar.
                       </div>';
            $mostrarTablaAprendices = true;
        }
    }

    // Cargar aprendices cuando se envía el formulario
    if (isset($_POST['ficha_id']) && !isset($_POST['buscar_asistencias'])) {
        $fichaId = $_POST['ficha_id'];
        $aprendices = obtenerAprendicesPorFicha($db, $fichaId);
        $mostrarTablaAprendices = true;

        // Si hay datos de asistencia previos, cargarlos
        if (isset($_POST['asistencia_data'])) {
            $asistenciaData = json_decode($_POST['asistencia_data'], true);
        }
    }
}

function obtenerAprendicesPorFicha($db, $fichaId)
{
    $stmt = $db->prepare("SELECT id, nombre FROM aprendices WHERE ficha_id = ?");
    $stmt->execute([$fichaId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerAsistenciasFiltradas($db, $fecha, $fichaId, $programaId)
{
    $sql = "SELECT a.id, a.fecha, a.hora_inicio, a.hora_fin, a.estado, ap.nombre AS aprendiz, f.codigo AS ficha, p.nombre AS programa 
            FROM asistencias a
            JOIN aprendices ap ON a.aprendiz_id = ap.id
            JOIN fichas f ON a.ficha_id = f.id
            JOIN programas_formacion p ON a.programa_id = p.id
            WHERE 1=1";

    $params = [];

    if (!empty($fecha)) {
        $sql .= " AND a.fecha = ?";
        $params[] = $fecha;
    }

    if (!empty($fichaId)) {
        $sql .= " AND a.ficha_id = ?";
        $params[] = $fichaId;
    }

    if (!empty($programaId)) {
        $sql .= " AND a.programa_id = ?";
        $params[] = $programaId;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$asistenciasFiltradas = [];
$mostrarBusqueda = false;
$realizoBusqueda = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_asistencias'])) {
    $tipoBusqueda = $_POST['tipo_busqueda'];
    $valorBusqueda = $_POST['valor_busqueda'];
    $realizoBusqueda = true;

    $fechaBusqueda = '';
    $fichaBusqueda = '';
    $programaBusqueda = '';

    switch ($tipoBusqueda) {
        case 'fecha':
            $fechaBusqueda = $valorBusqueda;
            break;
        case 'ficha':
            $fichaBusqueda = $valorBusqueda;
            break;
        case 'programa':
            $programaBusqueda = $valorBusqueda;
            break;
    }

    $asistenciasFiltradas = obtenerAsistenciasFiltradas($db, $fechaBusqueda, $fichaBusqueda, $programaBusqueda);
    $mostrarBusqueda = true;
}
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Registro de Asistencias</h1>

    <?php echo $mensaje; ?>

    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto mb-8">
        <form id="form-asistencia" method="POST" class="space-y-4">
            <div class="grid grid-cols-7 gap-4">
                <!-- Campos del formulario -->
                <div>
                    <label for="programa_formacion" class="block text-sm font-medium text-gray-700">Programa</label>
                    <select name="programa_formacion" id="programa_formacion" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($programas as $programa): ?>
                            <option value="<?php echo htmlspecialchars($programa['id']); ?>" <?php echo isset($_POST['programa_formacion']) && $_POST['programa_formacion'] == $programa['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($programa['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="ficha_id" class="block text-sm font-medium text-gray-700">Ficha</label>
                    <select name="ficha_id" id="ficha_id" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($fichas as $ficha): ?>
                            <option value="<?php echo htmlspecialchars($ficha['id']); ?>" <?php echo isset($_POST['ficha_id']) && $_POST['ficha_id'] == $ficha['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ficha['codigo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="ambiente_id" class="block text-sm font-medium text-gray-700">Ambiente</label>
                    <select name="ambiente_id" id="ambiente_id" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($ambientes as $ambiente): ?>
                            <option value="<?php echo htmlspecialchars($ambiente['id']); ?>" <?php echo isset($_POST['ambiente_id']) && $_POST['ambiente_id'] == $ambiente['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ambiente['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha" id="fecha" value="<?php echo isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : ''; ?>" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700">Hora Inicio</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" value="<?php echo isset($_POST['hora_inicio']) ? htmlspecialchars($_POST['hora_inicio']) : ''; ?>" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label for="hora_fin" class="block text-sm font-medium text-gray-700">Hora Fin</label>
                    <input type="time" name="hora_fin" id="hora_fin" value="<?php echo isset($_POST['hora_fin']) ? htmlspecialchars($_POST['hora_fin']) : ''; ?>" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="flex items-end space-x-3">
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-check"></i> Mostrar
                    </button>
                    <button type="button" id="btn-buscar-asistencias" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>

            <input type="hidden" name="asistencia_data" id="asistencia-data" value="<?php echo isset($_POST['asistencia_data']) ? htmlspecialchars($_POST['asistencia_data']) : ''; ?>">

            <?php if ($mostrarTablaAprendices && !empty($aprendices)): ?>
                <div id="asistencia-container" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto mt-6">
                    <h2 class="text-xl font-bold text-center mb-6">Lista de Aprendices</h2>

                    <table class="w-full border-collapse border border-gray-300 text-sm" id="tabla-aprendices">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 p-2">Nombre</th>
                                <th class="border border-gray-300 p-2">Programa</th>
                                <th class="border border-gray-300 p-2">Ficha</th>
                                <th class="border border-gray-300 p-2">Fecha</th>
                                <th class="border border-gray-300 p-2">Hora Inicio</th>
                                <th class="border border-gray-300 p-2">Hora Fin</th>
                                <th class="border border-gray-300 p-2">Ambiente</th>
                                <th class="border border-gray-300 p-2">Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $programa = $db->query("SELECT nombre FROM programas_formacion WHERE id = " . intval($_POST['programa_formacion']))->fetchColumn();
                            $ficha = $db->query("SELECT codigo FROM fichas WHERE id = " . intval($_POST['ficha_id']))->fetchColumn();
                            $ambiente = $db->query("SELECT nombre FROM ambientes WHERE id = " . intval($_POST['ambiente_id']))->fetchColumn();

                            foreach ($aprendices as $aprendiz):
                                $estadoActual = isset($asistenciaData[$aprendiz['id']]) ? $asistenciaData[$aprendiz['id']] : '';
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($aprendiz['nombre']) ?></td>
                                    <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($programa) ?></td>
                                    <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($ficha) ?></td>
                                    <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($_POST['fecha']) ?></td>
                                    <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($_POST['hora_inicio']) ?></td>
                                    <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($_POST['hora_fin']) ?></td>
                                    <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($ambiente) ?></td>
                                    <td class="border border-gray-300 p-2 text-center">
                                        <div class="flex space-x-2 justify-center">
                                            <button type="button" data-aprendiz-id="<?= $aprendiz['id'] ?>" data-estado="presente"
                                                class="asistencia-btn px-3 py-1 <?= $estadoActual === 'presente' ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-300 hover:bg-gray-400' ?> text-white rounded-md">
                                                Presente
                                            </button>
                                            <button type="button" data-aprendiz-id="<?= $aprendiz['id'] ?>" data-estado="ausente"
                                                class="asistencia-btn px-3 py-1 <?= $estadoActual === 'ausente' ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-300 hover:bg-gray-400' ?> text-white rounded-md">
                                                Ausente
                                            </button>
                                            <button type="button" data-aprendiz-id="<?= $aprendiz['id'] ?>" data-estado="excusa"
                                                class="asistencia-btn px-3 py-1 <?= $estadoActual === 'excusa' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-300 hover:bg-gray-400' ?> text-white rounded-md">
                                                Excusa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="mt-6 text-center">
                        <button type="submit" name="guardar_asistencia" class="px-6 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600">
                            <i class="fas fa-save mr-2"></i> Guardar Asistencia
                        </button>
                    </div>
                </div>
            <?php elseif ($mostrarTablaAprendices && empty($aprendices)): ?>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto mt-6 text-center text-red-500">
                    No se encontraron aprendices para esta ficha.
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Modal para búsqueda -->
    <div id="modal-busqueda" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Buscar Asistencias</h3>
                <button id="cerrar-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar por:</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="tipo-busqueda-btn px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300" data-tipo="fecha">Fecha</button>
                        <button type="button" class="tipo-busqueda-btn px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300" data-tipo="ficha">Ficha</button>
                        <button type="button" class="tipo-busqueda-btn px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300" data-tipo="programa">Programa</button>
                    </div>
                </div>

                <div id="campo-busqueda-fecha" class="hidden">
                    <label for="fecha_busqueda" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha_busqueda" id="fecha_busqueda" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div id="campo-busqueda-ficha" class="hidden">
                    <label for="ficha_busqueda" class="block text-sm font-medium text-gray-700">Ficha</label>
                    <select name="ficha_busqueda" id="ficha_busqueda" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione una ficha</option>
                        <?php foreach ($fichas as $ficha): ?>
                            <option value="<?php echo htmlspecialchars($ficha['id']); ?>"><?php echo htmlspecialchars($ficha['codigo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="campo-busqueda-programa" class="hidden">
                    <label for="programa_busqueda" class="block text-sm font-medium text-gray-700">Programa</label>
                    <select name="programa_busqueda" id="programa_busqueda" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un programa</option>
                        <?php foreach ($programas as $programa): ?>
                            <option value="<?php echo htmlspecialchars($programa['id']); ?>"><?php echo htmlspecialchars($programa['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="tipo_busqueda" id="tipo_busqueda">
                <input type="hidden" name="valor_busqueda" id="valor_busqueda">

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="btn-cancelar-busqueda" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" name="buscar_asistencias" id="btn-aplicar-busqueda" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados de búsqueda -->
    <?php if ($realizoBusqueda && !empty($asistenciasFiltradas)): ?>
        <div id="resultados-busqueda" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto mb-8">
            <h2 class="text-xl font-bold text-center mb-6">Resultados de la Búsqueda</h2>

            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-2">Aprendiz</th>
                        <th class="border border-gray-300 p-2">Programa</th>
                        <th class="border border-gray-300 p-2">Ficha</th>
                        <th class="border border-gray-300 p-2">Fecha</th>
                        <th class="border border-gray-300 p-2">Hora Inicio</th>
                        <th class="border border-gray-300 p-2">Hora Fin</th>
                        <th class="border border-gray-300 p-2">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asistenciasFiltradas as $asistencia): ?>
                        <tr>
                            <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($asistencia['aprendiz']); ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($asistencia['programa']); ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($asistencia['ficha']); ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($asistencia['fecha']); ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($asistencia['hora_inicio']); ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($asistencia['hora_fin']); ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($asistencia['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($realizoBusqueda && empty($asistenciasFiltradas)): ?>
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 max-w-full mx-auto mb-8 text-center text-red-500">
            No se encontraron resultados para la búsqueda.
        </div>
    <?php endif; ?>
</div>

<script>
    // Inicializar asistenciaSeleccionada con los datos del servidor o como objeto vacío
    let asistenciaSeleccionada = {};

    try {
        asistenciaSeleccionada = JSON.parse(document.getElementById('asistencia-data').value || '{}');
    } catch (e) {
        asistenciaSeleccionada = {};
    }

    // Manejar los botones de asistencia
    document.addEventListener('DOMContentLoaded', function() {
        // Asignar eventos a los botones de asistencia
        document.querySelectorAll('.asistencia-btn').forEach(boton => {
            boton.addEventListener('click', function() {
                const aprendizId = this.getAttribute('data-aprendiz-id');
                const estado = this.getAttribute('data-estado');
                marcarAsistencia(aprendizId, estado);
            });
        });

        // Inicializar estilos de los botones según los datos existentes
        Object.keys(asistenciaSeleccionada).forEach(aprendizId => {
            const estado = asistenciaSeleccionada[aprendizId];
            const boton = document.querySelector(`button[data-aprendiz-id="${aprendizId}"][data-estado="${estado}"]`);
            if (boton) {
                boton.classList.remove('bg-gray-300', 'hover:bg-gray-400');
                if (estado === 'presente') {
                    boton.classList.add('bg-green-500', 'hover:bg-green-600');
                } else if (estado === 'ausente') {
                    boton.classList.add('bg-red-500', 'hover:bg-red-600');
                } else if (estado === 'excusa') {
                    boton.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                }
            }
        });

        <?php if (!empty($mensaje)): ?>
            const mensajeElement = document.querySelector('.bg-green-100, .bg-red-100, .bg-yellow-100');
            if (mensajeElement) {
                mensajeElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

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

    function marcarAsistencia(aprendizId, estado) {
        // Actualizar el objeto de asistencia
        asistenciaSeleccionada[aprendizId] = estado;

        // Actualizar el campo hidden del formulario
        document.getElementById('asistencia-data').value = JSON.stringify(asistenciaSeleccionada);

        // Obtener todos los botones para este aprendiz
        const botones = document.querySelectorAll(`button[data-aprendiz-id="${aprendizId}"]`);

        // Restablecer todos los botones
        botones.forEach(boton => {
            boton.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500', 'hover:bg-green-600', 'hover:bg-red-600', 'hover:bg-yellow-600');
            boton.classList.add('bg-gray-300', 'hover:bg-gray-400');
        });

        // Resaltar el botón seleccionado
        const botonSeleccionado = document.querySelector(`button[data-aprendiz-id="${aprendizId}"][data-estado="${estado}"]`);
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

    // Manejo del modal de búsqueda
    document.getElementById('btn-buscar-asistencias').addEventListener('click', function() {
        document.getElementById('modal-busqueda').classList.remove('hidden');
    });

    document.getElementById('cerrar-modal').addEventListener('click', function() {
        document.getElementById('modal-busqueda').classList.add('hidden');
    });

    document.getElementById('btn-cancelar-busqueda').addEventListener('click', function() {
        document.getElementById('modal-busqueda').classList.add('hidden');
    });

    // Manejo de los botones de tipo de búsqueda
    document.querySelectorAll('.tipo-busqueda-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');

            document.querySelectorAll('.tipo-busqueda-btn').forEach(b => {
                b.classList.remove('bg-blue-500', 'text-white');
                b.classList.add('bg-gray-200');
            });

            document.querySelectorAll('[id^="campo-busqueda-"]').forEach(campo => {
                campo.classList.add('hidden');
            });

            this.classList.remove('bg-gray-200');
            this.classList.add('bg-blue-500', 'text-white');
            document.getElementById(`campo-busqueda-${tipo}`).classList.remove('hidden');
            document.getElementById('tipo_busqueda').value = tipo;
        });
    });

    // Actualizar el valor de búsqueda cuando cambia algún campo
    document.querySelectorAll('#fecha_busqueda, #ficha_busqueda, #programa_busqueda').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('valor_busqueda').value = this.value;
        });
    });

    // Validar antes de enviar
    document.getElementById('form-asistencia').addEventListener('submit', function(event) {
        // Solo validar si es el botón de guardar
        if (event.submitter && event.submitter.name === 'guardar_asistencia') {
            document.getElementById('asistencia-data').value = JSON.stringify(asistenciaSeleccionada);

            if (Object.keys(asistenciaSeleccionada).length === 0) {
                event.preventDefault();
                alert("Por favor, marque la asistencia de al menos un aprendiz.");
            }
        }
    });
</script>

<?php include '../partials/footer.php'; ?>