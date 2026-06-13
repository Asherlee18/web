<?php
require_once __DIR__ . "/../config/database.php";

class Venta {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function registrar($total) {
        $stmt = $this->conexion->prepare("INSERT INTO ventas (total) VALUES (?)");
        $stmt->bind_param("d", $total);
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    public function listar() {
        return $this->conexion->query("SELECT * FROM ventas ORDER BY fecha DESC");
    }

    public function listarPorDia($fecha) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM ventas WHERE DATE(fecha) = ? ORDER BY fecha DESC"
        );
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function listarPorMes($anio, $mes) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM ventas WHERE YEAR(fecha) = ? AND MONTH(fecha) = ? ORDER BY fecha DESC"
        );
        $stmt->bind_param("ii", $anio, $mes);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function totalPorDia($fecha) {
        $stmt = $this->conexion->prepare(
            "SELECT SUM(total) AS total, COUNT(*) AS cantidad FROM ventas WHERE DATE(fecha) = ?"
        );
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function totalPorMes($anio, $mes) {
        $stmt = $this->conexion->prepare(
            "SELECT SUM(total) AS total, COUNT(*) AS cantidad FROM ventas WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?"
        );
        $stmt->bind_param("ii", $anio, $mes);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function ventasHoy() {
        return $this->conexion->query(
            "SELECT SUM(total) AS total FROM ventas WHERE DATE(fecha) = CURDATE()"
        );
    }

    public function detalles($id_venta) {
        $stmt = $this->conexion->prepare(
            "SELECT dv.*, p.nombre AS producto 
             FROM detalle_ventas dv 
             JOIN productos p ON dv.id_producto = p.id 
             WHERE dv.id_venta = ?"
        );
        $stmt->bind_param("i", $id_venta);
        $stmt->execute();
        return $stmt->get_result();
    }
}
