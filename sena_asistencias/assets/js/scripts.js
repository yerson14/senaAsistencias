// Ejemplo: Script para el carrusel
document.addEventListener('DOMContentLoaded', function () {
    const carrusel = document.querySelector('.carrusel');
    if (carrusel) {
        let isDragging = false;
        let startX, scrollLeft;

        carrusel.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.pageX - carrusel.offsetLeft;
            scrollLeft = carrusel.scrollLeft;
        });

        carrusel.addEventListener('mouseleave', () => {
            isDragging = false;
        });

        carrusel.addEventListener('mouseup', () => {
            isDragging = false;
        });

        carrusel.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - carrusel.offsetLeft;
            const walk = (x - startX) * 2; // Ajusta la velocidad del arrastre
            carrusel.scrollLeft = scrollLeft - walk;
        });
    }
});