<?php
// Iniciar sesión
session_start();

// Incluir la conexión a la base de datos
require_once '../../config/database.php';

// Obtener la conexión
$db = Database::getInstance()->getConnection();

// Obtener aprendices con 3 o más inasistencias
$stmt = $db->prepare("
    SELECT a.id, a.nombre, COUNT(*) as total_ausentes 
    FROM asistencias asi
    JOIN aprendices a ON asi.aprendiz_id = a.id
    WHERE asi.estado = 'ausente'
    GROUP BY a.id
    HAVING COUNT(*) >= 3
");
$stmt->execute();
$aprendicesConInasistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Evitar caché -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Reportes de Asistencias</h1>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-green-600 to-green-500">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase">Nombre</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase">Total Ausencias</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase">Fechas de Ausencias</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (count($aprendicesConInasistencias) > 0): ?>
                        <?php foreach ($aprendicesConInasistencias as $aprendiz): ?>
                            <?php
                            // Obtener las fechas de ausencias para este aprendiz
                            $stmtFechas = $db->prepare("SELECT fecha FROM asistencias WHERE aprendiz_id = ? AND estado = 'ausente'");
                            $stmtFechas->execute([$aprendiz['id']]);
                            $fechasAusencias = $stmtFechas->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo $aprendiz['nombre']; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-red-500 rounded-full">
                                        <?php echo $aprendiz['total_ausentes']; ?> faltas
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="space-y-1">
                                        <?php foreach ($fechasAusencias as $fecha): ?>
                                            <span class="block px-3 py-1 bg-gray-100 rounded-md text-gray-700">
                                                <?php echo $fecha['fecha']; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                No hay aprendices con 3 o más inasistencias.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>