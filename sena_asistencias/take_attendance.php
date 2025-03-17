<?php
session_start();
require 'includes/functions.php';
checkPermission('instructor'); // Solo los instructores pueden tomar lista.

require 'includes/Database.php';
require 'includes/Instructor.php';

$instructor = new Instructor();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['ficha_id'], $_POST['fecha']) || empty($_POST['ficha_id']) || empty($_POST['fecha'])) {
        $error = "Debe seleccionar una ficha y una fecha.";
    } else {
        $ficha_id = $_POST['ficha_id'];
        $fecha = $_POST['fecha'];

        // Obtener todos los aprendices de esta ficha
        $todos_aprendices = $instructor->getAprendices($ficha_id);

        // Procesar cada aprendiz
        foreach ($todos_aprendices as $aprendiz) {
            $aprendiz_id = $aprendiz['id'];

            // Verificar si el aprendiz fue marcado como presente
            $asistio = isset($_POST['asistio'][$aprendiz_id]) ? 1 : 0;

            // Registrar asistencia para todos (presente o ausente)
            $instructor->tomarLista($aprendiz_id, $fecha, $asistio);
        }
        header('Location: dashboard.php');
        exit();
    }
}

$fichas = $instructor->getFichas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tomar Lista</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-green-50">

    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-10">
        <h1 class="text-3xl font-bold text-green-700 mb-6 text-center">Tomar Lista</h1>

        <div class="flex justify-end mb-4">
            <form action="dashboard.php" method="POST">
                <button type="submit"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold px-4 py-2 rounded shadow-md transition">
                    Regresar
                </button>
            </form>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-500 text-green-700 px-4 py-3 rounded shadow-md mb-4">
                <span><?php echo $_SESSION['success']; ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded shadow-md mb-4">
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="ficha_id" class="block text-gray-700 font-semibold">Seleccione una Ficha</label>
                <select id="ficha_id" name="ficha_id" required
                    class="border border-gray-300 shadow-sm rounded-lg w-full py-2 px-3 focus:ring focus:ring-green-300 bg-white">
                    <option value="">Seleccione una ficha</option>
                    <?php foreach ($fichas as $ficha): ?>
                        <option value="<?php echo htmlspecialchars($ficha['id']); ?>">
                            <?php echo htmlspecialchars($ficha['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="fecha" class="block text-gray-700 font-semibold">Fecha</label>
                <input id="fecha" name="fecha" type="date" required
                    class="border border-gray-300 shadow-sm rounded-lg w-full py-2 px-3 focus:ring focus:ring-green-300 bg-white">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Aprendices</label>
                <div id="aprendices-container" class="p-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm">
                    <p class="text-gray-600">Seleccione una ficha para ver los aprendices.</p>
                </div>
            </div>

            <div class="flex justify-center">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-md transition">
                    Tomar Lista
                </button>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('ficha_id').addEventListener('change', function () {
        let fichaId = this.value;
        let aprendicesContainer = document.getElementById('aprendices-container');

        if (fichaId) {
            fetch(`get_aprendices.php?ficha_id=${fichaId}`)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(aprendiz => {
                            html += `
                                <label class="flex items-center mb-2 p-2 bg-white border border-gray-300 rounded shadow-sm cursor-pointer">
                                    <input type="checkbox" id="aprendiz_${aprendiz.id}" name="asistio[${aprendiz.id}]" value="1" class="hidden">
                                    <span class="mr-2 w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                        <svg class="hidden w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="20 6 9 17 4 12" /> </svg>
                                    </span>
                                    <span class="text-gray-700">${aprendiz.name}</span>
                                </label>
                            `;
                        });
                    } else {
                        html = "<p class='text-red-500'>No hay aprendices en esta ficha.</p>";
                    }
                    aprendicesContainer.innerHTML = html;

                    // Agregar eventos para manejar clics en los labels
                    document.querySelectorAll('#aprendices-container label').forEach(label => {
                        label.addEventListener('click', function (event) {
                            let checkbox = this.querySelector('input[type=checkbox]');
                            checkbox.checked = !checkbox.checked;
                            let checkIcon = this.querySelector('svg');
                            if (checkbox.checked) {
                                checkIcon.classList.remove('hidden');
                            } else {
                                checkIcon.classList.add('hidden');
                            }
                            event.stopPropagation();
                        });
                    });
                })
                .catch(error => {
                    console.error('Error cargando aprendices:', error);
                    aprendicesContainer.innerHTML = "<p class='text-red-500'>Error al cargar los aprendices.</p>";
                });
        } else {
            aprendicesContainer.innerHTML = "<p class='text-gray-600'>Seleccione una ficha para ver los aprendices.</p>";
        }
    });
</script>


</body>

</html>
