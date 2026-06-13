<?php
require_once __DIR__ . "/../config/database.php";

class DetalleVenta {
    private $conexion;

    public function __construct(){
        global $conexion;
        $this->conexion = $conexion;
    }

    public function agregar($venta, $producto, $cantidad, $precio){
        $subtotal = $cantidad * $precio;

        $stmt = $this->conexion->prepare(
            "INSERT INTO detalle_ventas(id_venta, id_producto, cantidad, precio, subtotal)
             VALUES(?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iiidd", $venta, $producto, $cantidad, $precio, $subtotal);
        $stmt->execute();

        $stmt2 = $this->conexion->prepare(
            "UPDATE productos SET stock = stock - ? WHERE id = ?"
        );
        $stmt2->bind_param("ii", $cantidad, $producto);
        $stmt2->execute();
    }
}
