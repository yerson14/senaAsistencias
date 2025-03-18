<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/UsuarioModel.php';
require_once __DIR__ . '/../../models/CentroModel.php';

// Verificar si el usuario es superadmin
if ($_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../../index.php');
    exit;
}

$usuarioModel = new UsuarioModel();
$centroModel = new CentroModel();

// Obtener el ID del coordinador a editar
$id = $_GET['id'];

// Obtener la información del coordinador
$coordinador = $usuarioModel->obtenerCoordinadores(); // Obtener todos los coordinadores
$coordinador = array_filter($coordinador, function ($c) use ($id) {
    return $c['id'] == $id; // Filtrar por el ID del coordinador
});
$coordinador = reset($coordinador); // Obtener el primer resultado

// Verificar si el coordinador existe
if (!$coordinador) {
    $_SESSION['error'] = "Coordinador no encontrado.";
    header("Location: create_coordinador.php");
    exit();
}

// Obtener los centros disponibles
$centros = $centroModel->obtenerCentros();

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $numero_identificacion = $_POST['numero_identificacion'];
    $centro_id = $_POST['centro_id'];

    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id)) {
        $_SESSION['error'] = "Todos los campos son requeridos.";
        header("Location: editar_coordinador.php?id=" . $id);
        exit();
    }

    // Actualizar el coordinador
    if ($usuarioModel->editarCoordinador($id, $nombre, $correo, $numero_identificacion, $centro_id)) {
        $_SESSION['success'] = "Coordinador actualizado exitosamente.";
        header("Location: create_coordinador.php"); // Redirigir a create_coordinador.php
        exit();
    } else {
        $_SESSION['error'] = "Error al actualizar el coordinador.";
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
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
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
        <form action="editar_coordinador.php?id=<?php echo $id; ?>" method="POST">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($coordinador['nombre'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($coordinador['correo'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="numero_identificacion" class="block text-sm font-medium text-gray-700">Número de Identificación</label>
                <input type="text" name="numero_identificacion" id="numero_identificacion" value="<?php echo htmlspecialchars($coordinador['numero_identificacion'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="centro_id" class="block text-sm font-medium text-gray-700">Centro</label>
                <select name="centro_id" id="centro_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione un centro</option>
                    <?php foreach ($centros as $centro): ?>
                        <option value="<?php echo $centro['id']; ?>" <?php echo ($centro['id'] == ($coordinador['centro_id'] ?? '')) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($centro['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                Guardar Cambios
            </button>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>