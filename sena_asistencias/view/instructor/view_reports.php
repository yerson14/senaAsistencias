<?php
// Iniciar sesión
session_start();

// Verificar si el usuario es instructor
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'instructor') {
    die("Acceso no autorizado.");
}

// Incluir la conexión a la base de datos
require_once '../../config/database.php';

// Obtener la conexión
$db = Database::getInstance()->getConnection();

// Obtener los aprendices con 3 o más ausencias
$stmt = $db->prepare("SELECT aprendiz_id, COUNT(*) as total_ausencias FROM asistencias WHERE estado = 'ausente' GROUP BY aprendiz_id HAVING total_ausencias >= 3");
$stmt->execute();
$aprendices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Reportes de Asistencias</h1>
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500">ID Aprendiz</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500">Total de Ausencias</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($aprendices) > 0): ?>
                    <?php foreach ($aprendices as $aprendiz): ?>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-300"><?php echo $aprendiz['aprendiz_id']; ?></td>
                            <td class="px-6 py-4 border-b border-gray-300"><?php echo $aprendiz['total_ausencias']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="px-6 py-4 border-b border-gray-300 text-center">No hay aprendices con 3 o más ausencias.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>