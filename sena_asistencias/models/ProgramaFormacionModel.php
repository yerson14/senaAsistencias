<?php
require_once __DIR__ . '/../config/Database.php';

class ProgramaFormacionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Método para obtener todos los programas de formación
    public function obtenerProgramas()
    {
        try {
            $stmt = $this->db->prepare("SELECT pf.*, c.nombre as centro_nombre FROM programas_formacion pf JOIN centros c ON pf.centro_id = c.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los programas de formación: " . $e->getMessage());
        }
    }

    // Método para crear un nuevo programa de formación
    public function crearPrograma($nombre, $centro_id)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO programas_formacion (nombre, centro_id) VALUES (?, ?)");
            return $stmt->execute([$nombre, $centro_id]);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el programa de formación: " . $e->getMessage());
        }
    }

    // Método para editar un programa de formación
    public function editarPrograma($id, $nombre, $centro_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE programas_formacion SET nombre = ?, centro_id = ? WHERE id = ?");
            return $stmt->execute([$nombre, $centro_id, $id]);
        } catch (PDOException $e) {
            throw new Exception("Error al editar el programa de formación: " . $e->getMessage());
        }
    }

    // Método para eliminar un programa de formación
    public function eliminarPrograma($id)
    {
        try {
            // Eliminar fichas asociadas al programa
            $stmt = $this->db->prepare("DELETE FROM fichas WHERE programa_formacion_id = ?");
            $stmt->execute([$id]);

            // Eliminar el programa
            $stmt = $this->db->prepare("DELETE FROM programas_formacion WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el programa de formación: " . $e->getMessage());
        }
    }

    // Método para obtener un programa por su ID
    public function obtenerProgramaPorId($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM programas_formacion WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el programa: " . $e->getMessage());
        }
    }
    public function obtenerProgramasPorCoordinador($centro_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT pf.*, c.nombre as centro_nombre, r.nombre as regional_nombre 
                FROM programas_formacion pf 
                JOIN centros c ON pf.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                WHERE pf.centro_id = ?
                ORDER BY pf.nombre
            ");
            $stmt->execute([$centro_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los programas: " . $e->getMessage());
        }
    }
    
    public function obtenerProgramaPorIdConRegional($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT pf.*, c.nombre as centro_nombre, r.id as regional_id, r.nombre as regional_nombre 
                FROM programas_formacion pf 
                JOIN centros c ON pf.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                WHERE pf.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el programa: " . $e->getMessage());
        }
    }
}