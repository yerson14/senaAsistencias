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

    // Obtener todas las fichas
    public function getFichas() {
        $stmt = $this->db->prepare("SELECT id, nombre FROM fichas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener aprendices por ficha
    public function getAprendices($ficha_id) {
        $stmt = $this->db->prepare("SELECT id, nombre FROM aprendices WHERE ficha_id = ?");
        $stmt->execute([$ficha_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ambientes
    public function getAmbientes() {
        $stmt = $this->db->prepare("SELECT id, nombre FROM ambientes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener inasistencias de un aprendiz
    public function getInasistencias($aprendiz_id) {
        $stmt = $this->db->prepare("SELECT fecha FROM asistencias WHERE aprendiz_id = ? AND asistio = 0");
        $stmt->execute([$aprendiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar asistencia
    public function tomarLista($aprendiz_id, $fecha, $asistio) {
        $stmt = $this->db->prepare("INSERT INTO asistencias (aprendiz_id, fecha, asistio) VALUES (?, ?, ?)");
        return $stmt->execute([$aprendiz_id, $fecha, $asistio]);
    }
}
?>