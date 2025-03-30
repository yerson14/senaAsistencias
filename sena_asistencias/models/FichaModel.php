<?php
require_once __DIR__ . '/../config/Database.php';

class FichaModel
{
    private $db;

    public function __construct($db)
{
    $this->db = $db;
}

    // Crear una ficha
    public function crearFicha($numero, $programa_id)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($numero) || empty($programa_id)) {
                throw new Exception("El número de ficha y el ID del programa son requeridos.");
            }
    
            // Validar que el programa_id sea un número
            if (!is_numeric($programa_id)) {
                throw new Exception("El ID del programa debe ser un número.");
            }
    
            // Insertar la ficha
            $stmt = $this->db->prepare("INSERT INTO fichas (codigo, programa_formacion_id) VALUES (?, ?)");
            return $stmt->execute([$numero, $programa_id]);
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    // Obtener todas las fichas
    public function obtenerFichas()
    {
        try {
            $stmt = $this->db->prepare("SELECT f.*, p.nombre as programa_nombre FROM fichas f JOIN programas_formacion p ON f.programa_formacion_id = p.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las fichas: " . $e->getMessage());
        }
    }

    // Obtener una ficha por su ID
    public function obtenerFichaPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM fichas WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la ficha: " . $e->getMessage());
        }
    }

    // Editar una ficha
    public function editarFicha($id, $numero, $programa_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE fichas SET codigo = ?, programa_formacion_id = ? WHERE id = ?");
            return $stmt->execute([$numero, $programa_id, $id]);
        } catch (PDOException $e) {
            throw new Exception("Error al editar la ficha: " . $e->getMessage());
        }
    }

    // Eliminar una ficha
    public function eliminarFicha($id)
    {
        try {
            // Eliminar aprendices asociados a la ficha
            $stmt = $this->db->prepare("DELETE FROM aprendices WHERE ficha_id = ?");
            $stmt->execute([$id]);

            // Eliminar la ficha
            $stmt = $this->db->prepare("DELETE FROM fichas WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la ficha: " . $e->getMessage());
        }
    }
     // Obtener fichas con información completa (programa, centro y regional)
     public function obtenerFichasCompletas() {
        try {
            $stmt = $this->db->prepare("
                SELECT f.*, p.nombre as programa_nombre, 
                       c.nombre as centro_nombre, r.nombre as regional_nombre 
                FROM fichas f 
                JOIN programas_formacion p ON f.programa_formacion_id = p.id
                JOIN centros c ON p.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                ORDER BY f.codigo
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las fichas completas: " . $e->getMessage());
        }
    }

    // Obtener una ficha con información completa
    public function obtenerFichaCompleta($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT f.*, p.nombre as programa_nombre, 
                       c.nombre as centro_nombre, r.nombre as regional_nombre 
                FROM fichas f 
                JOIN programas_formacion p ON f.programa_formacion_id = p.id
                JOIN centros c ON p.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                WHERE f.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la ficha completa: " . $e->getMessage());
        }
    }
}