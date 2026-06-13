<?php
require_once __DIR__ . "/../config/database.php";

class Producto {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listar() {
        return $this->conexion->query("SELECT * FROM productos");
    }

    public function guardar($nombre, $stock, $precio) {
        $stmt = $this->conexion->prepare("INSERT INTO productos (nombre, stock, precio) VALUES (?, ?, ?)");
        $stmt->bind_param("sid", $nombre, $stock, $precio);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre, $stock, $precio) {
        $stmt = $this->conexion->prepare("UPDATE productos SET nombre = ?, stock = ?, precio = ? WHERE id = ?");
        $stmt->bind_param("sidi", $nombre, $stock, $precio, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}