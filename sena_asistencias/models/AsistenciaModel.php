    <?php
    require_once '../config/Database.php';

    class AsistenciaModel {
        private $db;

        public function __construct() {
            $this->db = Database::getInstance()->getConnection();
        }

        // Crear una nueva asistencia
        public function crearAsistencia($ficha_id, $ambiente_id, $instructor_id, $fecha, $estado) {
            $stmt = $this->db->prepare("INSERT INTO asistencias (ficha_id, ambiente_id, instructor_id, fecha, estado) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$ficha_id, $ambiente_id, $instructor_id, $fecha, $estado]);
        }

        // Obtener asistencias por ficha
        public function obtenerAsistenciasPorFicha($ficha_id) {
            $stmt = $this->db->prepare("SELECT * FROM asistencias WHERE ficha_id = ?");
            $stmt->execute([$ficha_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    ?>