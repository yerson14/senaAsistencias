<?php
require_once __DIR__ . '/../config/Database.php';

class UsuarioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Obtener un usuario por su número de identificación
    public function obtenerUsuarioPorIdentificacion($numero_identificacion) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE numero_identificacion = ?");
        $stmt->execute([$numero_identificacion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo usuario (para coordinadores e instructores)
    public function crearUsuario($nombre, $correo, $numero_identificacion, $rol) {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, correo, numero_identificacion, rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $correo, $numero_identificacion, $rol]);
    }

    // Obtener todos los coordinadores con información de centro y regional
    public function obtenerCoordinadores() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, u.nombre, u.correo, u.numero_identificacion, 
                    c.nombre AS centro_nombre, c.id AS centro_id,
                    r.nombre AS regional_nombre, r.id AS regional_id
                FROM usuarios u
                JOIN coordinadores co ON u.id = co.usuario_id
                JOIN centros c ON co.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                WHERE u.rol = 'coordinador'
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener coordinadores: " . $e->getMessage());
        }
    }

    // Editar un coordinador
    public function editarCoordinador($id, $nombre, $correo, $numero_identificacion, $centro_id) {
        try {
            // Actualizar la información del coordinador en la tabla de usuarios
            $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, correo = ?, numero_identificacion = ? WHERE id = ?");
            if ($stmt->execute([$nombre, $correo, $numero_identificacion, $id])) {
                // Actualizar la asignación del coordinador al centro
                $stmt = $this->db->prepare("UPDATE coordinadores SET centro_id = ? WHERE usuario_id = ?");
                return $stmt->execute([$centro_id, $id]);
            } else {
                throw new Exception("Error al actualizar el coordinador.");
            }
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Obtener un coordinador por ID con información de centro y regional
    public function obtenerCoordinadorPorId($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.*, 
                    c.id AS centro_id, c.nombre AS centro_nombre,
                    r.id AS regional_id, r.nombre AS regional_nombre
                FROM usuarios u
                JOIN coordinadores co ON u.id = co.usuario_id
                JOIN centros c ON co.centro_id = c.id
                JOIN regionales r ON c.regional_id = r.id
                WHERE u.id = ? AND u.rol = 'coordinador'
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener coordinador: " . $e->getMessage());
        }
    }
}
?>