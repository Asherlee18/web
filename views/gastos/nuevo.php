<?php
require_once __DIR__ . "/../../helpers/auth.php";
checkSesion('administradora');
?>
<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8">
    <title>Nuevo Gasto - Juguería Aitana</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head><body>
<?php include __DIR__ . "/../layouts/sidebar_admin.php"; ?>
<div class="topbar">
    <span class="topbar-title">Nuevo Gasto</span>
    <div class="topbar-user"><div class="avatar"><?= strtoupper(substr($_SESSION['nombre'],0,1)) ?></div></div>
</div>
<div class="content">
    <div class="page-header"><h1>Registrar Gasto</h1></div>
    <div class="card" style="max-width:480px">
        <form action="../../controllers/GastoController.php" method="POST">
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" placeholder="Ej: Compra de frutas" required>
            </div>
            <div class="form-group">
                <label>Monto (S/)</label>
                <input type="number" step="0.01" name="monto" placeholder="0.00" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Gasto</button>
            <a href="listar.php" class="btn btn-secondary" style="margin-left:8px">Cancelar</a>
        </form>
    </div>
</div>
</body></html>
