<?php
require_once __DIR__ . '/../config/Database.php';

class CentroModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo centro
    public function crearCentro($nombre, $regional_id) {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($regional_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Preparar la consulta SQL
            $stmt = $this->db->prepare("INSERT INTO centros (nombre, regional_id) VALUES (?, ?)");

            // Ejecutar la consulta
            if ($stmt->execute([$nombre, $regional_id])) {
                return true; // Éxito
            } else {
                throw new Exception("Error al crear el centro.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Obtener centros por regional
    public function obtenerRegionales() {
        try {
            $stmt = $this->db->query("SELECT id, nombre FROM regionales ORDER BY nombre");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerRegionales: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerRegionalConCentros($regional_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT r.id as regional_id, r.nombre as regional_nombre, 
                       c.id as centro_id, c.nombre as centro_nombre 
                FROM regionales r
                LEFT JOIN centros c ON r.id = c.regional_id
                WHERE r.id = ?
            ");
            $stmt->execute([$regional_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerRegionalConCentros: " . $e->getMessage());
            return [];
        }
    }

    // Obtener todos los centros
    public function obtenerCentros() {
        try {
            // Preparar la consulta SQL
            $stmt = $this->db->query("SELECT c.id, c.nombre, r.nombre as regional_nombre 
                                     FROM centros c 
                                     JOIN regionales r ON c.regional_id = r.id");

            // Verificar si se obtuvieron resultados
            if ($stmt) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Error al obtener los centros.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    public function editarCentro($id, $nombre, $regional_id) {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($regional_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }
    
            // Preparar la consulta SQL
            $stmt = $this->db->prepare("UPDATE centros SET nombre = ?, regional_id = ? WHERE id = ?");
    
            // Ejecutar la consulta
            if ($stmt->execute([$nombre, $regional_id, $id])) {
                return true; // Éxito
            } else {
                throw new Exception("Error al actualizar el centro.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    public function obtenerCentroPorId($id) {
        try {
            // Preparar la consulta SQL
            $stmt = $this->db->prepare("SELECT * FROM centros WHERE id = ?");
            $stmt->execute([$id]);
    
            // Verificar si se obtuvo un resultado
            if ($stmt) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Error al obtener el centro.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    public function eliminarCentro($id) {
        try {
            // Preparar la consulta SQL
            $stmt = $this->db->prepare("DELETE FROM centros WHERE id = ?");
    
            // Ejecutar la consulta
            if ($stmt->execute([$id])) {
                return true; // Éxito
            } else {
                throw new Exception("Error al eliminar el centro.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    public function obtenerCentrosPorRegional($regional_id) {
        try {
            $stmt = $this->db->prepare("SELECT id, nombre FROM centros WHERE regional_id = ?");
            $stmt->execute([$regional_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener centros por regional: " . $e->getMessage());
            return false;
        }
    }
}