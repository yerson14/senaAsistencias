<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA - Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/sidebar.php'; ?>

    <div class="ml-0 md:ml-64 p-8 transition-all duration-200 ease-in-out">
       
        <!-- Carrusel de Imágenes -->
        <div class="carrusel">
            <img src="../../assets/images/carrusel1.jpg" alt="Imagen 1" class="w-full h-64 object-cover rounded-lg">
            <img src="../../assets/images/carrusel2.jpg" alt="Imagen 2" class="w-full h-64 object-cover rounded-lg">
            <img src="../../assets/images/carrusel3.jpg" alt="Imagen 3" class="w-full h-64 object-cover rounded-lg">
        </div>
    </div>

    <!-- Script para el menú hamburguesa -->
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>

    <?php include '../partials/footer.php'; ?>
</body>
</html>