<?php
require_once __DIR__ . '/../config/Database.php';

class AprendizModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearAprendiz($nombre, $numero_identificacion, $ficha_id, $centro_id, $regional_id) {
        try {
            $stmt = $this->db->prepare("INSERT INTO aprendices (nombre, numero_identificacion, ficha_id, centro_id, regional_id) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$nombre, $numero_identificacion, $ficha_id, $centro_id, $regional_id]);
        } catch (PDOException $e) {
            throw new Exception("Error al crear aprendiz: " . $e->getMessage());
        }
    }

    public function obtenerAprendices() {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, f.codigo as ficha_numero, c.nombre as centro_nombre 
                FROM aprendices a
                JOIN fichas f ON a.ficha_id = f.id
                JOIN centros c ON a.centro_id = c.id
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener aprendices: " . $e->getMessage());
        }
    }

    public function obtenerAprendizPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM aprendices WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener aprendiz: " . $e->getMessage());
        }
    }

    public function actualizarAprendiz($id, $nombre, $numero_identificacion, $ficha_id, $centro_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE aprendices 
                SET nombre = ?, numero_identificacion = ?, ficha_id = ?, centro_id = ?
                WHERE id = ?
            ");
            return $stmt->execute([$nombre, $numero_identificacion, $ficha_id, $centro_id, $id]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar aprendiz: " . $e->getMessage());
        }
    }

    public function eliminarAprendiz($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM aprendices WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar aprendiz: " . $e->getMessage());
        }
    }
}