<?php
require_once __DIR__ . '/../config/Database.php';

class AmbienteModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }


    public function obtenerAmbientesCompletos() {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, c.nombre as centro_nombre, r.nombre as regional_nombre 
                FROM ambientes a 
                JOIN centros c ON a.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                ORDER BY a.nombre
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener ambientes completos: " . $e->getMessage());
        }
    }

    // Obtener un ambiente con información completa
    public function obtenerAmbienteCompleto($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, c.nombre as centro_nombre, r.nombre as regional_nombre, r.id as regional_id 
                FROM ambientes a 
                JOIN centros c ON a.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener ambiente completo: " . $e->getMessage());
        }
    }
    // Crear un nuevo ambiente
    public function crearAmbiente($nombre, $centro_id) {
        try {
            $stmt = $this->db->prepare("INSERT INTO ambientes (nombre, centro_id) VALUES (?, ?)");
            return $stmt->execute([$nombre, $centro_id]);
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Obtener ambientes por centro
    public function obtenerAmbientesPorCentro($centro_id) {
        $stmt = $this->db->prepare("SELECT * FROM ambientes WHERE centro_id = ?");
        $stmt->execute([$centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    // Obtener todos los ambientes
    public function obtenerAmbientes()
    {
        try {
            $stmt = $this->db->prepare("SELECT a.*, c.nombre as centro_nombre FROM ambientes a JOIN centros c ON a.centro_id = c.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los ambientes: " . $e->getMessage());
        }
    }
    
    public function obtenerAmbientePorId($id)
{
    try {
        $stmt = $this->db->prepare("SELECT a.*, c.nombre as centro_nombre FROM ambientes a JOIN centros c ON a.centro_id = c.id WHERE a.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al obtener el ambiente: " . $e->getMessage());
    }
}

public function editarAmbiente($id, $nombre, $centro_id)
{
    try {
        $stmt = $this->db->prepare("UPDATE ambientes SET nombre = ?, centro_id = ? WHERE id = ?");
        return $stmt->execute([$nombre, $centro_id, $id]);
    } catch (PDOException $e) {
        throw new Exception("Error al editar el ambiente: " . $e->getMessage());
    }
}

public function eliminarAmbiente($id)
{
    try {
        $stmt = $this->db->prepare("DELETE FROM ambientes WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        throw new Exception("Error al eliminar el ambiente: " . $e->getMessage());
    }
}
}
?>