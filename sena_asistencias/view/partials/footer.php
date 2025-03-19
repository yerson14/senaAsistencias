<footer class="bg-green-600 shadow-md p-2 text-center text-white" id="footer">
    <p>&copy; 2024 SENA. Todos los derechos reservados.</p>
    <p>Servicio Nacional de Aprendizaje - SENA</p>
</footer>

<script>
    // Script para manejar el comportamiento del footer
    document.addEventListener("DOMContentLoaded", function() {
        const footer = document.getElementById("footer");
        const body = document.body;
        const html = document.documentElement;

        // Función para verificar si el contenido es más alto que la ventana
        function adjustFooter() {
            const windowHeight = window.innerHeight;
            const bodyHeight = body.scrollHeight;

            if (bodyHeight > windowHeight) {
                // Si el contenido es más alto que la ventana, el footer se desplaza
                footer.style.position = "relative";
            } else {
                // Si el contenido es corto, el footer se pega al final de la pantalla
                footer.style.position = "fixed";
                footer.style.bottom = "0";
                footer.style.left = "0";
                footer.style.right = "0";
            }
        }

        // Ajustar el footer al cargar la página y al cambiar el tamaño de la ventana
        adjustFooter();
        window.addEventListener("resize", adjustFooter);
    });
</script>