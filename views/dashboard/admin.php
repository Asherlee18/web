<?php
require_once __DIR__ . "/../../helpers/auth.php";
checkSesion('administradora');
require_once __DIR__ . "/../../models/venta.php";
require_once __DIR__ . "/../../models/gasto.php";
require_once __DIR__ . "/../../models/producto.php";

$ventaModel    = new Venta();
$gastoModel    = new Gasto();
$productoModel = new Producto();

$ventasHoy      = $ventaModel->ventasHoy()->fetch_assoc()['total'] ?? 0;
$totalGastos    = $gastoModel->totalGastos()->fetch_assoc()['total'] ?? 0;
$productos      = $productoModel->listar();
$totalProductos = $productos->num_rows;

$modalError = '';
$modalExito = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_producto'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock  = intval($_POST['stock'] ?? 0);
    if ($nombre && $precio > 0) {
        $productoModel->guardar($nombre, $stock, $precio);
        $modalExito = "Producto \"$nombre\" registrado correctamente.";
        $totalProductos = $productoModel->listar()->num_rows;
    } else {
        $modalError = "Completa todos los campos correctamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Juguería Aitana</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            background: rgba(10, 40, 20, 0.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open {
            display: flex;
        }
        .modal {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 20px;
            padding: 36px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 60px rgba(10,40,20,0.25);
            position: relative;
            animation: modalIn .22s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(18px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-close {
            position: absolute;
            top: 16px; right: 18px;
            background: rgba(20,92,52,0.08);
            border: none;
            width: 30px; height: 30px;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
            color: #145c34;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .modal-close:hover { background: rgba(20,92,52,0.18); }
        .modal-header { margin-bottom: 24px; }
        .modal-header .modal-icon {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, #1a6b3f, #2d9b5c);
            border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 12px;
        }
        .modal-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #0f3d22;
            margin: 0 0 2px;
        }
        .modal-header p {
            font-size: 13px;
            color: #888;
            margin: 0;
        }
        .modal .form-group { margin-bottom: 16px; }
        .modal .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #145c34;
            letter-spacing: .4px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .modal .form-group input,
        .modal .form-group select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d0ddd6;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: rgba(255,255,255,0.8);
            color: #1a1a2e;
            outline: none;
            transition: border-color .18s, box-shadow .18s;
            box-sizing: border-box;
        }
        .modal .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%23145c34' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-color: rgba(255,255,255,0.8);
        }
        .modal .form-group input:focus,
        .modal .form-group select:focus {
            border-color: #145c34;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(20,92,52,0.1);
        }
        .modal-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
        }
        .btn-modal-submit {
            flex: 1;
            padding: 12px;
            background: linear-gradient(135deg, #145c34, #1d7a46);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: opacity .18s, transform .18s;
        }
        .btn-modal-submit:hover {
            opacity: .9;
            transform: translateY(-1px);
        }
        .btn-modal-cancel {
            padding: 12px 18px;
            background: rgba(20,92,52,0.07);
            color: #145c34;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-modal-cancel:hover { background: rgba(20,92,52,0.14); }
        .modal-alert {
            padding: 10px 14px;
            border-radius: 0 9px 9px 0;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .modal-alert.exito { background: #e8f8ef; color: #0f6e56; border-left: 3px solid #1d9e75; }
        .modal-alert.error { background: #fcebeb; color: #791f1f; border-left: 3px solid #a32d2d; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h2>🍹 Juguería Aitana</h2>
        <span>Administradora</span>
    </div>
    <div class="sidebar-menu">
        <p class="menu-label">Menú</p>
        <a href="admin.php" class="active"><span class="icon">🏠</span> Dashboard</a>
        <a href="../ventas/listar.php"><span class="icon">🛒</span> Ventas</a>
        <a href="../productos/listar.php"><span class="icon">📦</span> Productos</a>
        <a href="../gastos/listar.php"><span class="icon">💸</span> Gastos</a>
        <a href="../reportes/diario.php"><span class="icon">📊</span> Reportes</a>
    </div>
    <div class="sidebar-footer">
        <a href="../../logout.php">⬅ Cerrar Sesión</a>
    </div>
</div>

<div class="topbar">
    <span class="topbar-title">Dashboard</span>
    <div class="topbar-user">
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nombre'],0,1)); ?></div>
        <?php echo htmlspecialchars($_SESSION['nombre']); ?>
    </div>
</div>

<div class="content">
    <div class="page-header">
        <h1>Bienvenida 👋</h1>
        <p>Resumen del día — <?php echo date('d/m/Y'); ?></p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green">🛒</div>
            <div class="stat-value">S/ <?php echo number_format($ventasHoy, 2); ?></div>
            <div class="stat-label">Ventas de hoy</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">💸</div>
            <div class="stat-value">S/ <?php echo number_format($totalGastos, 2); ?></div>
            <div class="stat-label">Total gastos</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">📦</div>
            <div class="stat-value"><?php echo $totalProductos; ?></div>
            <div class="stat-label">Productos</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <p class="card-title">Accesos rápidos</p>
        </div>
        <a href="../ventas/nueva.php" class="btn btn-primary" style="margin-right:8px">Nueva Venta</a>
        <button onclick="abrirModal()" class="btn btn-secondary" style="margin-right:8px">Nuevo Producto</button>
        <a href="../gastos/nuevo.php" class="btn btn-secondary">Registrar Gasto</a>
    </div>
</div>

<!-- MODAL NUEVO PRODUCTO -->
<div class="modal-overlay <?php echo ($modalError || $modalExito) ? 'open' : ''; ?>" id="modalOverlay">
    <div class="modal">
        <button class="modal-close" onclick="cerrarModal()">✕</button>

        <div class="modal-header">
            <div class="modal-icon">📦</div>
            <h3>Nuevo Producto</h3>
            <p>Agrega un producto al catálogo</p>
        </div>

        <?php if ($modalExito): ?>
            <div class="modal-alert exito">✅ <?php echo htmlspecialchars($modalExito); ?></div>
        <?php endif; ?>
        <?php if ($modalError): ?>
            <div class="modal-alert error">⚠️ <?php echo htmlspecialchars($modalError); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="nuevo_producto" value="1">

            <div class="form-group">
                <label>Nombre del producto</label>
                <input type="text" name="nombre" placeholder="Ej: Jugo de mango" required autofocus>
            </div>

            <div class="modal-row">
                <div class="form-group">
                    <label>Precio (S/)</label>
                    <input type="number" name="precio" step="0.01" min="0.01" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Disponibilidad</label>
                    <select name="stock" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="1">Disponible</option>
                        <option value="0">No disponible</option>
                    </select>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-modal-submit">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModal() {
        document.getElementById('modalOverlay').classList.add('open');
        setTimeout(() => document.querySelector('.modal input[name="nombre"]').focus(), 100);
    }
    function cerrarModal() {
        document.getElementById('modalOverlay').classList.remove('open');
    }
    document.getElementById('modalOverlay').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cerrarModal();
    });
    <?php if ($modalExito): ?>
        document.getElementById('modalOverlay').classList.add('open');
        setTimeout(() => cerrarModal(), 2500);
    <?php endif; ?>
</script>

</body>
</html>