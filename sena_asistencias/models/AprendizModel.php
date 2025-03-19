<?php
require_once __DIR__ . '/../config/Database.php';

class AprendizModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo aprendiz
    public function crearAprendiz($nombre, $numero_identificacion, $ficha_id, $centro_id) {
        try {
            $stmt = $this->db->prepare("INSERT INTO aprendices (nombre, numero_identificacion, ficha_id, centro_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $numero_identificacion, $ficha_id, $centro_id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    // Obtener todos los aprendices
    public function obtenerAprendices() {
        try {
            $stmt = $this->db->prepare("SELECT a.id, a.nombre, a.numero_identificacion, f.codigo as ficha_numero 
                                        FROM aprendices a 
                                        JOIN fichas f ON a.ficha_id = f.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
            return [];
        }
    }

    // Obtener un aprendiz por ID
    public function obtenerAprendizPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT a.id, a.nombre, a.numero_identificacion, a.ficha_id 
                                        FROM aprendices a 
                                        WHERE a.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Actualizar un aprendiz
    public function actualizarAprendiz($id, $nombre, $numero_identificacion, $ficha_id) {
        try {
            $stmt = $this->db->prepare("UPDATE aprendices SET nombre = ?, numero_identificacion = ?, ficha_id = ? WHERE id = ?");
            $stmt->execute([$nombre, $numero_identificacion, $ficha_id, $id]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Eliminar un aprendiz
    public function eliminarAprendiz($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM aprendices WHERE id = ?");
            $stmt->execute([$id]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    public function obtenerFichas() {
        $stmt = $this->db->prepare("SELECT f.id, f.numero_ficha 
                                    FROM fichas f
                                    LEFT JOIN programa_formacion pf ON f.programa_formacion_id = pf.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerAprendicesConFicha() {
        $aprendices = $this->obtenerAprendices();

        if ($this->fichaModel) {
            foreach ($aprendices as &$aprendiz) {
                $ficha = $this->fichaModel->obtenerFichaPorId($aprendiz['ficha_id']);
                $aprendiz['ficha_info'] = $ficha;
            }
        }

        return $aprendices;
    }
}
?>
