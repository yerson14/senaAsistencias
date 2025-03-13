<?php
require_once '../config/Database.php';

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
}
?>