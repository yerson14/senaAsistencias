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
            // Insertar el instructor en la tabla de usuarios
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
}