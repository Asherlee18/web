<?php
require_once __DIR__ . "/../config/database.php";

class Gasto {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function registrar($descripcion, $monto) {
        $stmt = $this->conexion->prepare("INSERT INTO gastos (descripcion, monto, fecha) VALUES (?, ?, CURDATE())");
        $stmt->bind_param("sd", $descripcion, $monto);
        return $stmt->execute();
    }

    public function listar() {
        return $this->conexion->query("SELECT * FROM gastos ORDER BY fecha DESC");
    }

    public function totalGastos() {
        return $this->conexion->query("SELECT SUM(monto) AS total FROM gastos");
    }
}