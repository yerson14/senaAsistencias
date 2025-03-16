<?php
require_once __DIR__ . '/../config/Database.php';

class RegionalModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear una nueva regional
    public function crearRegional($nombre) {
        try {
            // Validar que el nombre no esté vacío
            if (empty($nombre)) {
                throw new Exception("El nombre de la regional es requerido.");
            }

            // Preparar la consulta SQL
            $stmt = $this->db->prepare("INSERT INTO regionales (nombre) VALUES (?)");

            // Ejecutar la consulta
            if ($stmt->execute([$nombre])) {
                return true; // Éxito
            } else {
                throw new Exception("Error al crear la regional.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        } catch (Exception $e) {
            // Capturar otros errores
            throw new Exception($e->getMessage());
        }
    }

    // Obtener todas las regionales
    public function obtenerRegionales() {
        try {
            // Preparar la consulta SQL
            $stmt = $this->db->query("SELECT * FROM regionales");

            // Verificar si se obtuvieron resultados
            if ($stmt) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Error al obtener las regionales.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Obtener una regional por su ID
    public function obtenerRegionalPorId($id) {
        try {
            // Preparar la consulta SQL
            $stmt = $this->db->prepare("SELECT * FROM regionales WHERE id = ?");
            $stmt->execute([$id]);

            // Verificar si se obtuvo un resultado
            if ($stmt) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Error al obtener la regional.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Editar una regional
    public function editarRegional($id, $nombre) {
        try {
            // Validar que el nombre no esté vacío
            if (empty($nombre)) {
                throw new Exception("El nombre de la regional es requerido.");
            }

            // Preparar la consulta SQL
            $stmt = $this->db->prepare("UPDATE regionales SET nombre = ? WHERE id = ?");

            // Ejecutar la consulta
            if ($stmt->execute([$nombre, $id])) {
                return true; // Éxito
            } else {
                throw new Exception("Error al actualizar la regional.");
            }
        } catch (PDOException $e) {
            // Capturar errores de la base de datos
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Eliminar una regional
   // Eliminar una regional
public function eliminarRegional($id) {
    try {
        // Preparar la consulta SQL
        $stmt = $this->db->prepare("DELETE FROM regionales WHERE id = ?");

        // Ejecutar la consulta
        if ($stmt->execute([$id])) {
            return true; // Éxito
        } else {
            throw new Exception("Error al eliminar la regional.");
        }
    } catch (PDOException $e) {
        // Capturar errores de la base de datos
        throw new Exception("Error de base de datos: " . $e->getMessage());
    }
}
}