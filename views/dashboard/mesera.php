<?php
require_once __DIR__ . "/../../helpers/auth.php";
checkSesion('mesera');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Mesera - Juguería Aitana</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h2>🍹 Juguería Aitana</h2>
        <span>Mesera</span>
    </div>
    <div class="sidebar-menu">
        <a href="../ventas/nueva.php" class="active">
            <span class="icon">🛒</span> Nueva Venta
        </a>
        <a href="../ventas/listar.php">
            <span class="icon">📋</span> Ventas del Día
        </a>
    </div>
    <div class="sidebar-footer">
        <a href="../../logout.php">⬅ Cerrar Sesión</a>
    </div>
</div>

<div class="content">
    <div class="page-header">
        <h1>Bienvenida, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
        <p>Panel de ventas</p>
    </div>
    <div class="card">
        <p class="card-title">Nueva Venta</p>
        <p style="color:#888;font-size:13px;margin-bottom:12px">
            Selecciona los jugos, calcula el total y registra la venta.
        </p>
        <a href="../ventas/nueva.php" class="btn btn-primary">Registrar Venta</a>
    </div>
</div>

</body>
</html>
