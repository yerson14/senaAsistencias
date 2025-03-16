<?php
session_start();
if ($_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../../index.php');
    exit;
}

include '../../models/RegionalModel.php';
$regionalModel = new RegionalModel();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $regional = $regionalModel->obtenerRegionalPorId($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre de la regional es obligatorio.";
        header('Location: editar_regional.php?id=' . $id);
        exit;
    }

    $result = $regionalModel->editarRegional($id, $nombre);

    if ($result) {
        $_SESSION['success'] = "Regional actualizada exitosamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar la regional.";
    }

    header('Location: create_regional.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Regional</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Editar Regional</h1>

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

        <!-- Formulario para editar regional -->
        <form action="editar_regional.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $regional['id']; ?>">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Regional</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo $regional['nombre']; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                Guardar Cambios
            </button>
        </form>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>