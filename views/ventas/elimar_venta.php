<?php
require_once __DIR__ . "/../../helpers/auth.php";
checkSesion('administradora');
require_once __DIR__ . "/../../config/database.php";

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Eliminar detalles primero (FK), luego la venta
    $stmt = $conexion->prepare("DELETE FROM detalle_ventas WHERE id_venta = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt2 = $conexion->prepare("DELETE FROM ventas WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
}

// Redirigir manteniendo el filtro activo
$filtro = $_GET['filtro'] ?? 'hoy';
$fecha  = $_GET['fecha']  ?? date('Y-m-d');
$mes    = $_GET['mes']    ?? date('m');
$anio   = $_GET['anio']  ?? date('Y');

header("Location: listar.php?filtro=$filtro&fecha=$fecha&mes=$mes&anio=$anio&eliminado=1");
exit();