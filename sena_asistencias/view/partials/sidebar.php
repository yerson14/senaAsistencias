<?php
session_start(); // Iniciar la sesión
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-blue-800 text-white overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out z-40">
    <div class="p-4">
        <h2 class="text-2xl font-bold">SENA</h2>
    </div>
    <nav class="mt-6">
        <ul>
            <li class="p-4 hover:bg-blue-700"><a href="../superadmin/index.php">Inicio</a></li>
            <?php if ($_SESSION['usuario']['rol'] === 'superadmin'): ?>
                <li class="p-4 hover:bg-blue-700"><a href="../superadmin/create_regional.php">Crear Regional</a></li>
                <li class="p-4 hover:bg-blue-700"><a href="../superadmin/create_center.php">Crear Centro</a></li>
                <li class="p-4 hover:bg-blue-700"><a href="../superadmin/create_coordinador.php">Crear Coordinador</a></li>
            <?php elseif ($_SESSION['usuario']['rol'] === 'coordinador'): ?>
                <li class="p-4 hover:bg-blue-700"><a href="../coordinator/create_program.php">Crear Programa</a></li>
                <li class="p-4 hover:bg-blue-700"><a href="../coordinator/create_ficha.php">Crear Ficha</a></li>
                <li class="p-4 hover:bg-blue-700"><a href="../coordinator/create_ambiente.php">Crear Ambiente</a></li>
                <li class="p-4 hover:bg-blue-700"><a href="../coordinator/create_instructor.php">Crear Instructor</a></li>
            <?php elseif ($_SESSION['usuario']['rol'] === 'instructor'): ?>
                <li class="p-4 hover:bg-blue-700"><a href="../instructor/take_attendance.php">Tomar Lista</a></li>
                <li class="p-4 hover:bg-blue-700"><a href="../instructor/view_reports.php">Ver Reportes</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>