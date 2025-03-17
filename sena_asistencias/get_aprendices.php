<?php
require_once 'includes/Database.php';
require_once 'controllers/InstructorController.php';

$instructorController = new InstructorController();

if (isset($_GET['ficha_id'])) {
    $ficha_id = $_GET['ficha_id'];
    $aprendices = $instructorController->obtenerAprendices($ficha_id);
    echo json_encode($aprendices);
} else {
    echo json_encode([]);
}
?>