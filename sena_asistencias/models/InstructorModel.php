<?php
require_once __DIR__ . '/../config/Database.php';

class InstructorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un nuevo instructor
    public function crearInstructor($nombre, $correo, $numero_identificacion, $centro_id) {
        try {
            // Insertar en la tabla de usuarios
            $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, correo, numero_identificacion, rol) VALUES (?, ?, ?, 'instructor')");
            $stmt->execute([$nombre, $correo, $numero_identificacion]);

            // Obtener el ID del usuario creado
            $usuario_id = $this->db->lastInsertId();

            // Asignar el instructor a un centro
            $stmt = $this->db->prepare("INSERT INTO instructores (usuario_id, centro_id) VALUES (?, ?)");
            $stmt->execute([$usuario_id, $centro_id]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Obtener todos los instructores
    public function obtenerInstructores() {
        $stmt = $this->db->prepare("SELECT u.id, u.nombre, u.correo, u.numero_identificacion, c.nombre as centro_nombre 
                                    FROM usuarios u 
                                    JOIN instructores i ON u.id = i.usuario_id 
                                    JOIN centros c ON i.centro_id = c.id 
                                    WHERE u.rol = 'instructor'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un instructor por ID
    public function obtenerInstructorPorId($id) {
        $stmt = $this->db->prepare("SELECT u.id, u.nombre, u.correo, u.numero_identificacion, i.centro_id 
                                    FROM usuarios u 
                                    JOIN instructores i ON u.id = i.usuario_id 
                                    WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un instructor
    public function actualizarInstructor($id, $nombre, $correo, $numero_identificacion, $centro_id) {
        try {
            // Actualizar en la tabla de usuarios
            $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, correo = ?, numero_identificacion = ? WHERE id = ?");
            $stmt->execute([$nombre, $correo, $numero_identificacion, $id]);

            // Actualizar en la tabla de instructores
            $stmt = $this->db->prepare("UPDATE instructores SET centro_id = ? WHERE usuario_id = ?");
            $stmt->execute([$centro_id, $id]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }

    // Eliminar un instructor
    public function eliminarInstructor($id) {
        try {
            // Eliminar de la tabla de instructores
            $stmt = $this->db->prepare("DELETE FROM instructores WHERE usuario_id = ?");
            $stmt->execute([$id]);

            // Eliminar de la tabla de usuarios
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
}
?>