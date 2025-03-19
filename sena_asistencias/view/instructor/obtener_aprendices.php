<?php
// Incluir la conexión a la base de datos
require_once '../../config/database.php';

// Obtener la conexión
$db = Database::getInstance()->getConnection();

// Verificar que se recibió el parámetro ficha_id
if (isset($_GET['ficha_id'])) {
    $fichaId = $_GET['ficha_id'];
    
    try {
        // Preparar y ejecutar la consulta para obtener los aprendices de la ficha
        $stmt = $db->prepare("SELECT id, nombre FROM aprendices WHERE ficha_id = ?");
        $stmt->execute([$fichaId]);
        $aprendices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Establecer headers para JSON
        header('Content-Type: application/json');
        echo json_encode($aprendices);
    } catch (Exception $e) {
        // En caso de error
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // Si no se recibió el parámetro ficha_id
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Se requiere el parámetro ficha_id']);
}
?>