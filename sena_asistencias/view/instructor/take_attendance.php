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

            <!-- Lista de estudiantes -->
            <div id="lista-estudiantes" class="mt-4"></div>

        </form>
    </div>
</div>

<script>
    function cargarEstudiantes(fichaId) {
        if (fichaId) {
            fetch(`../../controllers/InstructorController.php?action=get_estudiantes&ficha_id=${fichaId}`)
                .then(response => response.json())
                .then(data => {
                    const listaEstudiantes = document.getElementById('lista-estudiantes');
                    listaEstudiantes.innerHTML = '';

                    if (data.length > 0) {
                        const ul = document.createElement('ul');
                        ul.className = 'space-y-2';

                        data.forEach(estudiante => {
                            const li = document.createElement('li');
                            li.className = 'bg-gray-100 p-2 rounded-md';
                            li.textContent = `${estudiante.nombre} ${estudiante.apellido}`;
                            ul.appendChild(li);
                        });

                        listaEstudiantes.appendChild(ul);
                    } else {
                        listaEstudiantes.innerHTML = '<p class="text-center text-gray-500">No hay estudiantes en esta ficha.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los estudiantes:', error);
                });
        } else {
            document.getElementById('lista-estudiantes').innerHTML = '';
        }
    }
</script>

<?php include '../partials/footer.php'; ?>
