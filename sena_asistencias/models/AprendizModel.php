<?php
require_once __DIR__ . '/../config/Database.php';

class AprendizModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearAprendiz($nombre, $numero_identificacion, $ficha_id, $centro_id, $regional_id) {
        try {
            // Primero obtener el programa_formacion_id de la ficha
            $stmt = $this->db->prepare("SELECT programa_formacion_id FROM fichas WHERE id = ?");
            $stmt->execute([$ficha_id]);
            $ficha = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$ficha) {
                throw new Exception("La ficha especificada no existe.");
            }
    
            $programa_formacion_id = $ficha['programa_formacion_id'];
    
            $stmt = $this->db->prepare(
                "INSERT INTO aprendices 
                (nombre, numero_identificacion, ficha_id, centro_id, regional_id, programa_formacion_id) 
                VALUES (?, ?, ?, ?, ?, ?)"
            );
    
            if ($stmt->execute([$nombre, $numero_identificacion, $ficha_id, $centro_id, $regional_id, $programa_formacion_id])) {
                return true;
            } else {
                throw new Exception("Error al crear el aprendiz.");
            }
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
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