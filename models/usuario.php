<?php
require_once __DIR__ . "/../config/database.php";

class Usuario {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listar() {
        return $this->conexion->query("SELECT * FROM usuarios");
    }

    public function guardar($usuario, $clave) {
        // Encriptamos la contraseña por seguridad antes de guardarla
        $claveEncriptada = password_hash($clave, PASSWORD_BCRYPT);

        $stmt = $this->conexion->prepare("INSERT INTO usuarios (usuario, clave) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $claveEncriptada);
        return $stmt->execute();
    }
}