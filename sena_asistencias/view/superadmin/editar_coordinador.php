<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/UsuarioModel.php';
require_once __DIR__ . '/../../models/CentroModel.php';
require_once __DIR__ . '/../../models/RegionalModel.php';

// Verificar si el usuario es superadmin
if ($_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../../index.php');
    exit;
}

$usuarioModel = new UsuarioModel();
$centroModel = new CentroModel();
$regionalModel = new RegionalModel();

// Obtener el ID del coordinador a editar
$id = $_GET['id'];

// Obtener la información completa del coordinador
$coordinador = $usuarioModel->obtenerCoordinadorPorId($id);

// Verificar si el coordinador existe
if (!$coordinador) {
    $_SESSION['error'] = "Coordinador no encontrado.";
    header("Location: create_coordinador.php");
    exit();
}

// Obtener datos necesarios para el formulario
$regionales = $regionalModel->obtenerRegionales();
$centros = $centroModel->obtenerCentros();
$centrosRegionalActual = $centroModel->obtenerCentrosPorRegional($coordinador['regional_id']);

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $numero_identificacion = $_POST['numero_identificacion'];
    $centro_id = $_POST['centro_id'];
    $regional_id = $_POST['regional_id'];

    try {
        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id) || empty($regional_id)) {
            throw new Exception("Todos los campos son requeridos.");
        }

        // Actualizar el coordinador
        if ($usuarioModel->editarCoordinador($id, $nombre, $correo, $numero_identificacion, $centro_id, $regional_id)) {
            $_SESSION['success'] = "Coordinador actualizado exitosamente.";
            header("Location: create_coordinador.php");
            exit();
        } else {
            throw new Exception("Error al actualizar el coordinador.");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: editar_coordinador.php?id=" . $id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Coordinador</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        select:disabled {
            background-color: #f3f4f6;
            cursor: not-allowed;
        }
        #centro-container {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8 flex-grow">
        <h1 class="text-3xl font-bold mb-6">Editar Coordinador</h1>

        <!-- Mensajes de éxito o error -->
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

        <!-- Formulario para editar coordinador -->
        <form action="editar_coordinador.php?id=<?php echo $id; ?>" method="POST" class="max-w-lg">
            <!-- Campo Nombre -->
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" 
                       value="<?php echo htmlspecialchars($coordinador['nombre'] ?? ''); ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            
            <!-- Campo Correo -->
            <div class="mb-4">
                <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" name="correo" id="correo" 
                       value="<?php echo htmlspecialchars($coordinador['correo'] ?? ''); ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            
            <!-- Campo Identificación -->
            <div class="mb-4">
                <label for="numero_identificacion" class="block text-sm font-medium text-gray-700">Número de Identificación</label>
                <input type="text" name="numero_identificacion" id="numero_identificacion" 
                       value="<?php echo htmlspecialchars($coordinador['numero_identificacion'] ?? ''); ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            
            <!-- Selector de Regional -->
            <div class="mb-4">
                <label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label>
                <select name="regional_id" id="regional_id" 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                        required
                        onchange="cargarCentrosPorRegional(this.value, <?php echo $coordinador['centro_id'] ?? 'null'; ?>)">
                    <option value="">Seleccione una regional</option>
                    <?php foreach ($regionales as $regional): ?>
                        <option value="<?php echo htmlspecialchars($regional['id']); ?>" 
                            <?php echo ($regional['id'] == $coordinador['regional_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($regional['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Selector de Centros -->
            <div id="centro-container" class="mb-4">
                <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro de Formación</label>
                <select name="centro_id" id="centro_id" 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                        required>
                    <?php foreach ($centrosRegionalActual as $centro): ?>
                        <option value="<?php echo htmlspecialchars($centro['id']); ?>"
                            <?php echo ($centro['id'] == $coordinador['centro_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($centro['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="centro-mensaje" class="mt-2 text-sm text-red-600 hidden"></div>
            </div>
            
            <!-- Botones del formulario -->
            <div class="flex space-x-2">
                <a href="create_coordinador.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <script>
    // Función para cargar centros según la regional seleccionada
    function cargarCentrosPorRegional(regionalId, centroSeleccionado = null) {
        const centroSelect = document.getElementById('centro_id');
        const centroMensaje = document.getElementById('centro-mensaje');

        // Resetear estado
        centroMensaje.classList.add('hidden');
        centroMensaje.textContent = '';
        centroSelect.innerHTML = '<option value="">Cargando centros...</option>';
        centroSelect.disabled = true;
        
        if (!regionalId) {
            centroSelect.innerHTML = '<option value="">Seleccione una regional primero</option>';
            return;
        }
        
        // Realizar petición AJAX
        fetch(`../../controllers/SuperAdminController.php?action=obtener_centros_por_regional&regional_id=${regionalId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data.success && data.centros && data.centros.length > 0) {
                    let options = '';
                    data.centros.forEach(centro => {
                        const selected = centroSeleccionado && centro.id == centroSeleccionado ? 'selected' : '';
                        options += `<option value="${centro.id}" ${selected}>${centro.nombre}</option>`;
                    });
                    centroSelect.innerHTML = options;
                    centroSelect.disabled = false;
                    centroMensaje.classList.add('hidden');
                } else {
                    centroSelect.innerHTML = '<option value="">No hay centros disponibles</option>';
                    centroSelect.disabled = true;
                    centroMensaje.textContent = 'Esta regional no tiene centros disponibles';
                    centroMensaje.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                centroSelect.innerHTML = '<option value="">Error al cargar centros</option>';
                centroSelect.disabled = true;
                centroMensaje.textContent = 'Error al cargar los centros. Por favor intente nuevamente.';
                centroMensaje.classList.remove('hidden');
            });
    }
    </script>

    <?php include '../partials/footer.php'; ?>
</body>
</html>