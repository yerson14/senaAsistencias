<?php
require_once __DIR__ .'../../config/Database.php'; // Asegúrate de que la ruta sea correcta


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
            $stmt = $this->db->prepare("SELECT * FROM programas_formacion");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejar errores de base de datos
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
            // Manejar errores de base de datos
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
            // Manejar errores de base de datos
            throw new Exception("Error al editar el programa de formación: " . $e->getMessage());
        }
    }

    // Método para eliminar un programa de formación
    public function eliminarPrograma($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM programas_formacion WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Manejar errores de base de datos
            throw new Exception("Error al eliminar el programa de formación: " . $e->getMessage());
        }
    }
}