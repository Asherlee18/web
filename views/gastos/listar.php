<?php
require_once __DIR__ . "/../../helpers/auth.php";
checkSesion('administradora');
require_once __DIR__ . "/../../models/gasto.php";
$model = new Gasto();
$gastos = $model->listar();
?>
<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8">
    <title>Gastos - Juguería Aitana</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head><body>
<?php include __DIR__ . "/../layouts/sidebar_admin.php"; ?>
<div class="topbar">
    <span class="topbar-title">Gastos</span>
    <div class="topbar-user"><div class="avatar"><?= strtoupper(substr($_SESSION['nombre'],0,1)) ?></div><?= htmlspecialchars($_SESSION['nombre']) ?></div>
</div>
<div class="content">
    <div class="page-header"><h1>Gastos</h1><p>Registro de gastos del negocio</p></div>
    <div class="card">
        <div class="card-header">
            <p class="card-title">Lista de gastos</p>
            <a href="nuevo.php" class="btn btn-primary btn-sm">+ Nuevo Gasto</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>ID</th><th>Descripción</th><th>Monto</th><th>Fecha</th></tr></thead>
                <tbody>
                <?php while($g = $gastos->fetch_assoc()): ?>
                <tr>
                    <td><?= $g['id'] ?></td>
                    <td><?= htmlspecialchars($g['descripcion']) ?></td>
                    <td>S/ <?= number_format($g['monto'],2) ?></td>
                    <td><?= date('d/m/Y', strtotime($g['fecha'])) ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body></html>
