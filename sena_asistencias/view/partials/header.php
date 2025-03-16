<header class="top-0 left-0 bg-green-600 shadow-md p-4 flex justify-between items-center">
    <!-- Botón del menú hamburguesa -->
    <button id="menu-toggle" class="text-white hover:text-gray-200 focus:outline-none">
        <i class="fas fa-bars text-2xl"></i> <!-- Ícono de menú hamburguesa -->
    </button>

    <!-- Contenedor para los íconos de SENA, perfil y logout -->
    <div class="flex items-center space-x-4">
        <!-- Ícono del SENA -->
        <img src="ruta/a/logo-sena.png" alt="Logo SENA" class="h-8 w-8"> <!-- Cambia la ruta a la de tu logo -->
        
        <!-- Ícono de perfil -->
        <a href="ruta/a/perfil" class="text-white hover:text-gray-200">
            <i class="fas fa-user-circle text-2xl"></i> <!-- Ícono de perfil -->
        </a>

        <!-- Ícono de cerrar sesión -->
        <a href="../../controllers/AuthController.php?action=logout" class="text-white hover:text-gray-200">
            <i class="fas fa-sign-out-alt text-2xl"></i> <!-- Ícono de cerrar sesión -->
        </a>
    </div>
</header>

<script>
    // Función para abrir/cerrar el sidebar
    document.getElementById('menu-toggle').addEventListener('click', function(event) {
        event.stopPropagation(); // Evita que el clic se propague al documento
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    });

    // Función para cerrar el sidebar al hacer clic fuera de él
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menu-toggle');

        // Verifica si el clic fue fuera del sidebar y del botón del menú
        if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
            sidebar.classList.add('-translate-x-full'); // Cierra el sidebar
        }
    });
</script>