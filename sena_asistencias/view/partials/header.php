<header class="top-0 left-0  bg-white shadow-md p-4 flex justify-between items-cente">
    <!-- Botón del menú hamburguesa -->
    <button id="menu-toggle" class="text-blue-500 hover:text-blue-700 focus:outline-none">
        <i class="fas fa-bars text-2xl"></i> <!-- Ícono de menú hamburguesa -->
    </button>

    <!-- Ícono de cerrar sesión -->
    <a href="../../controllers/AuthController.php?action=logout" class="text-blue-500 hover:text-blue-700">
        <i class="fas fa-sign-out-alt text-2xl"></i> <!-- Ícono de cerrar sesión -->
    </a>
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