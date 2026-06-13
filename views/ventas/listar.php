<?php
require_once __DIR__ . "/../../helpers/auth.php";
checkSesion();
require_once __DIR__ . "/../../models/venta.php";
require_once __DIR__ . "/../../models/producto.php";

$ventaModel    = new Venta();
$productoModel = new Producto();

// ── REGISTRAR NUEVA VENTA ──────────────────────────────────────────
$ventaExito = '';
$ventaError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_venta'])) {
    $productos_ids = $_POST['producto_id']  ?? [];
    $cantidades    = $_POST['cantidad']     ?? [];
    $precios       = $_POST['precio_unit']  ?? [];
    $total         = floatval($_POST['total_venta'] ?? 0);

    if (!empty($productos_ids) && $total > 0) {
        require_once __DIR__ . "/../../models/detalleventa.php";
        $detalleModel = new DetalleVenta();
        $id_venta = $ventaModel->registrar($total);
        foreach ($productos_ids as $i => $pid) {
            $cant   = intval($cantidades[$i]);
            $precio = floatval($precios[$i]);
            if ($cant > 0 && $pid > 0) {
                $detalleModel->agregar($id_venta, $pid, $cant, $precio);
            }
        }
        header("Location: listar.php?exito=1&id=" . $id_venta);
        exit();
    } else {
        $ventaError = "Agrega al menos un producto con cantidad válida.";
    }
}

// Mensaje de éxito tras redirect
if (isset($_GET['exito']) && isset($_GET['id'])) {
    $ventaExito = "Venta #" . intval($_GET['id']) . " registrada correctamente.";
}

// ── FILTROS ────────────────────────────────────────────────────────
$filtro = $_GET['filtro'] ?? 'hoy';
$fecha  = $_GET['fecha']  ?? date('Y-m-d');
$mes    = intval($_GET['mes']  ?? date('m'));
$anio   = intval($_GET['anio'] ?? date('Y'));

if ($filtro === 'dia') {
    $ventas  = $ventaModel->listarPorDia($fecha);
    $resumen = $ventaModel->totalPorDia($fecha);
} elseif ($filtro === 'mes') {
    $ventas  = $ventaModel->listarPorMes($anio, $mes);
    $resumen = $ventaModel->totalPorMes($anio, $mes);
} else {
    $fecha   = date('Y-m-d');
    $ventas  = $ventaModel->listarPorDia($fecha);
    $resumen = $ventaModel->totalPorDia($fecha);
    $filtro  = 'hoy';
}

$todosProductos = $productoModel->listar();
$productos_lista = [];
while ($p = $todosProductos->fetch_assoc()) $productos_lista[] = $p;

$meses_nombres = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas - Juguería Aitana</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <style>
        .filter-bar {
            display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:16px;
        }
        .filter-btn {
            padding:8px 18px; border-radius:20px; border:1.5px solid #d0ddd6;
            font-size:13px; font-family:inherit; cursor:pointer;
            background:#fff; color:#555; transition:all .18s; font-weight:500;
            text-decoration:none; display:inline-block;
        }
        .filter-btn.active { background:#145c34; border-color:#145c34; color:#fff; }
        .filter-btn:hover:not(.active) { border-color:#145c34; color:#145c34; }
        .filter-inputs { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .filter-inputs input[type="date"],
        .filter-inputs select {
            padding:7px 12px; border:1.5px solid #d0ddd6; border-radius:8px;
            font-size:13px; font-family:inherit; color:#333; outline:none;
            transition:border-color .18s;
        }
        .filter-inputs input:focus,
        .filter-inputs select:focus { border-color:#145c34; }
        .btn-filter-go {
            padding:7px 16px; background:#145c34; color:#fff;
            border:none; border-radius:8px; font-size:13px;
            font-family:inherit; cursor:pointer; transition:opacity .15s;
        }
        .btn-filter-go:hover { opacity:.85; }
        .resumen-bar { display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
        .resumen-item {
            background:#fff; border:0.5px solid #e8edf2; border-radius:12px;
            padding:14px 20px; display:flex; flex-direction:column; gap:2px;
        }
        .resumen-item .r-val { font-size:22px; font-weight:600; color:#145c34; }
        .resumen-item .r-lbl { font-size:12px; color:#888; }

        /* MODAL */
        .modal-overlay {
            display:none; position:fixed; inset:0; z-index:999;
            background:rgba(10,40,20,0.55);
            backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
            align-items:center; justify-content:center;
        }
        .modal-overlay.open { display:flex; }
        .modal {
            background:rgba(255,255,255,0.93);
            backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,0.6); border-radius:20px;
            padding:30px 34px; width:100%; max-width:540px;
            box-shadow:0 24px 60px rgba(10,40,20,0.25);
            position:relative; animation:modalIn .22s ease;
            max-height:92vh; overflow-y:auto;
        }
        @keyframes modalIn {
            from { opacity:0; transform:translateY(16px) scale(.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }
        .modal-close {
            position:absolute; top:16px; right:18px;
            background:rgba(20,92,52,0.08); border:none;
            width:30px; height:30px; border-radius:50%;
            font-size:16px; cursor:pointer; color:#145c34;
            display:flex; align-items:center; justify-content:center;
            transition:background .15s;
        }
        .modal-close:hover { background:rgba(20,92,52,0.18); }
        .modal-icon {
            width:46px; height:46px;
            background:linear-gradient(135deg,#1a6b3f,#2d9b5c);
            border-radius:13px; display:flex; align-items:center;
            justify-content:center; font-size:22px; margin-bottom:10px;
        }
        .modal h3 { font-size:18px; font-weight:600; color:#0f3d22; margin:0 0 2px; }
        .modal .sub { font-size:13px; color:#888; margin:0 0 20px; }
        .item-row {
            display:grid; grid-template-columns:1fr 80px 90px 32px;
            gap:8px; align-items:center; margin-bottom:8px;
        }
        .item-row select, .item-row input {
            padding:9px 11px; border:1.5px solid #d0ddd6; border-radius:9px;
            font-size:13px; font-family:inherit; outline:none; width:100%;
            box-sizing:border-box; transition:border-color .18s;
        }
        .item-row select:focus, .item-row input:focus { border-color:#145c34; }
        .btn-remove {
            width:30px; height:30px; border-radius:50%; border:none;
            background:rgba(220,50,50,0.08); color:#c0392b;
            font-size:16px; cursor:pointer; display:flex;
            align-items:center; justify-content:center; transition:background .15s;
            flex-shrink:0;
        }
        .btn-remove:hover { background:rgba(220,50,50,0.18); }
        .item-header {
            display:grid; grid-template-columns:1fr 80px 90px 32px;
            gap:8px; margin-bottom:4px;
        }
        .item-header span {
            font-size:11px; font-weight:600; color:#145c34;
            text-transform:uppercase; letter-spacing:.4px;
        }
        .btn-add-item {
            display:flex; align-items:center; gap:6px;
            background:rgba(20,92,52,0.07); border:1.5px dashed #b5d5c0;
            color:#145c34; padding:9px 14px; border-radius:9px;
            font-size:13px; font-family:inherit; cursor:pointer;
            transition:background .15s; margin-bottom:16px; width:100%;
            justify-content:center; font-weight:500;
        }
        .btn-add-item:hover { background:rgba(20,92,52,0.13); }
        .total-box {
            background:rgba(20,92,52,0.06); border-radius:10px;
            padding:12px 16px; display:flex; justify-content:space-between;
            align-items:center; margin-bottom:18px;
        }
        .total-box .tl { font-size:13px; color:#555; font-weight:500; }
        .total-box .tv { font-size:22px; font-weight:700; color:#145c34; }
        .modal-actions { display:flex; gap:10px; }
        .btn-modal-submit {
            flex:1; padding:12px;
            background:linear-gradient(135deg,#145c34,#1d7a46);
            color:#fff; border:none; border-radius:10px;
            font-size:14px; font-weight:600; font-family:inherit;
            cursor:pointer; transition:opacity .18s,transform .18s;
        }
        .btn-modal-submit:hover { opacity:.9; transform:translateY(-1px); }
        .btn-modal-cancel {
            padding:12px 18px; background:rgba(20,92,52,0.07);
            color:#145c34; border:none; border-radius:10px;
            font-size:14px; font-family:inherit; cursor:pointer;
        }
        .btn-modal-cancel:hover { background:rgba(20,92,52,0.14); }
        .modal-alert {
            padding:10px 14px; border-radius:0 9px 9px 0;
            font-size:13px; margin-bottom:14px;
            display:flex; align-items:center; gap:8px;
        }
        .modal-alert.exito { background:#e8f8ef; color:#0f6e56; border-left:3px solid #1d9e75; }
        .modal-alert.error { background:#fcebeb; color:#791f1f; border-left:3px solid #a32d2d; }
        .empty-state {
            text-align:center; padding:40px 20px; color:#aaa; font-size:14px;
        }
        .empty-state .es-icon { font-size:36px; margin-bottom:10px; }
    </style>
</head>
<body>

<?php include __DIR__ . "/../layouts/sidebar_admin.php"; ?>

<div class="topbar">
    <span class="topbar-title">Ventas</span>
    <div class="topbar-user">
        <div class="avatar"><?= strtoupper(substr($_SESSION['nombre'],0,1)) ?></div>
        <?= htmlspecialchars($_SESSION['nombre']) ?>
    </div>
</div>

<div class="content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <h1>Ventas</h1>
            <p>Registro y consulta de ventas</p>
        </div>
        <button onclick="abrirModal('modalVenta')" class="btn btn-primary">+ Nueva Venta</button>
    </div>

    <?php if ($ventaExito): ?>
        <div class="modal-alert exito" style="border-radius:10px;margin-bottom:16px">
            ✅ <?= htmlspecialchars($ventaExito) ?>
        </div>
    <?php endif; ?>
    <?php if ($ventaError): ?>
        <div class="modal-alert error" style="border-radius:10px;margin-bottom:16px">
            ⚠️ <?= htmlspecialchars($ventaError) ?>
        </div>
    <?php endif; ?>

    <!-- FILTROS -->
    <div class="card" style="margin-bottom:16px;padding:16px 20px">
        <div class="filter-bar">
            <a href="listar.php?filtro=hoy" class="filter-btn <?= $filtro==='hoy'?'active':'' ?>">📅 HOY</a>
            <button type="button" onclick="toggleFiltro('dia',this)" class="filter-btn <?= $filtro==='dia'?'active':'' ?>">📆 DIA</button>
            <button type="button" onclick="toggleFiltro('mes',this)" class="filter-btn <?= $filtro==='mes'?'active':'' ?>">🗓️ MES</button>
        </div>

        <form method="GET" action="listar.php" id="formFiltro"
              style="display:<?= ($filtro==='dia'||$filtro==='mes')?'block':'none' ?>">
            <input type="hidden" name="filtro" id="hiddenFiltro" value="<?= htmlspecialchars($filtro) ?>">

            <div class="filter-inputs" id="inputDia" style="display:<?= $filtro==='dia'?'flex':'none' ?>">
                <span style="font-size:13px;color:#555;font-weight:500">Selecciona el día:</span>
                <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" max="<?= date('Y-m-d') ?>">
                <button type="submit" class="btn-filter-go">Filtrar</button>
            </div>

            <div class="filter-inputs" id="inputMes" style="display:<?= $filtro==='mes'?'flex':'none' ?>">
                <span style="font-size:13px;color:#555;font-weight:500">Selecciona el mes:</span>
                <select name="mes">
                    <?php for($m=1;$m<=12;$m++): ?>
                        <option value="<?= $m ?>" <?= $m==$mes?'selected':'' ?>><?= $meses_nombres[$m] ?></option>
                    <?php endfor; ?>
                </select>
                <select name="anio">
                    <?php for($a=date('Y');$a>=date('Y')-3;$a--): ?>
                        <option value="<?= $a ?>" <?= $a==$anio?'selected':'' ?>><?= $a ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn-filter-go">Filtrar</button>
            </div>
        </form>
    </div>

    <!-- RESUMEN -->
    <div class="resumen-bar">
        <div class="resumen-item">
            <span class="r-val">S/ <?= number_format($resumen['total'] ?? 0, 2) ?></span>
            <span class="r-lbl">
                <?php
                    if ($filtro==='mes') echo "Total " . $meses_nombres[$mes] . " $anio";
                    elseif ($filtro==='dia') echo "Total " . date('d/m/Y', strtotime($fecha));
                    else echo "Total hoy";
                ?>
            </span>
        </div>
        <div class="resumen-item">
            <span class="r-val"><?= intval($resumen['cantidad'] ?? 0) ?></span>
            <span class="r-lbl">Ventas del período</span>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card">
        <div class="card-header">
            <p class="card-title">
                <?php
                    if ($filtro==='mes') echo "Ventas de " . $meses_nombres[$mes] . " $anio";
                    elseif ($filtro==='dia') echo "Ventas del " . date('d/m/Y', strtotime($fecha));
                    else echo "Ventas de hoy — " . date('d/m/Y');
                ?>
            </p>
        </div>
        <div class="table-wrapper">
            <?php $ventas_arr = []; while($v = $ventas->fetch_assoc()) $ventas_arr[] = $v; ?>
            <?php if (empty($ventas_arr)): ?>
                <div class="empty-state">
                    <div class="es-icon">🧃</div>
                    No hay ventas registradas en este período.
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Fecha</th><th>Hora</th><th>Total</th><th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($ventas_arr as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($v['fecha'])) ?></td>
                    <td><?= date('H:i', strtotime($v['fecha'])) ?></td>
                    <td><strong>S/ <?= number_format($v['total'],2) ?></strong></td>
                    <td>
                        <button onclick="verDetalle(<?= $v['id'] ?>)"
                                class="btn btn-secondary btn-sm">Ver detalle</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL NUEVA VENTA -->
<div class="modal-overlay" id="modalVenta">
    <div class="modal">
        <button class="modal-close" onclick="cerrarModal('modalVenta')">✕</button>
        <div class="modal-icon">🛒</div>
        <h3>Nueva Venta</h3>
        <p class="sub">Selecciona los productos y cantidades</p>

        <form method="POST" action="listar.php" id="formVenta">
            <input type="hidden" name="registrar_venta" value="1">
            <input type="hidden" name="total_venta" id="totalInput" value="0">

            <div class="item-header">
                <span>Producto</span><span>Cantidad</span><span>Precio</span><span></span>
            </div>
            <div id="itemsContainer"></div>

            <button type="button" class="btn-add-item" onclick="agregarItem()">
                ＋ Agregar producto
            </button>

            <div class="total-box">
                <span class="tl">Total a cobrar</span>
                <span class="tv" id="totalDisplay">S/ 0.00</span>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="cerrarModal('modalVenta')">Cancelar</button>
                <button type="submit" class="btn-modal-submit">Registrar Venta</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETALLE -->
<div class="modal-overlay" id="modalDetalle">
    <div class="modal" style="max-width:480px">
        <button class="modal-close" onclick="cerrarModal('modalDetalle')">✕</button>
        <div class="modal-icon">🧾</div>
        <h3>Detalle de Venta</h3>
        <p class="sub" id="detalleSubtitle">Cargando...</p>
        <div id="detalleContenido"></div>
    </div>
</div>


<script>
const productos = <?= json_encode($productos_lista) ?>;

function abrirModal(id) {
    document.getElementById(id).classList.add('open');
    if (id === 'modalVenta' && document.querySelectorAll('.item-row').length === 0) {
        agregarItem();
    }
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('open');
}
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if(e.target===o) o.classList.remove('open'); });
});
document.addEventListener('keydown', e => {
    if(e.key==='Escape') document.querySelectorAll('.modal-overlay.open')
        .forEach(o => o.classList.remove('open'));
});

function agregarItem() {
    const container = document.getElementById('itemsContainer');
    const opts = productos.map(p =>
        `<option value="${p.id}" data-precio="${p.precio}">${p.nombre} (S/ ${parseFloat(p.precio).toFixed(2)})</option>`
    ).join('');
    const row = document.createElement('div');
    row.className = 'item-row';
    row.innerHTML = `
        <select name="producto_id[]" onchange="actualizarPrecio(this)" required>
            <option value="">-- Producto --</option>${opts}
        </select>
        <input type="number" name="cantidad[]" value="1" min="1"
               oninput="calcularTotal()" placeholder="Cant." required>
        <input type="number" name="precio_unit[]" step="0.01" min="0"
               placeholder="Precio" readonly
               style="background:rgba(20,92,52,0.04);color:#145c34;font-weight:600">
        <button type="button" class="btn-remove" onclick="eliminarItem(this)">×</button>
    `;
    container.appendChild(row);
}

function eliminarItem(btn) {
    if (document.querySelectorAll('.item-row').length <= 1) return;
    btn.closest('.item-row').remove();
    calcularTotal();
}

function actualizarPrecio(select) {
    const opt = select.options[select.selectedIndex];
    const precio = opt.dataset.precio || '';
    select.closest('.item-row').querySelector('input[name="precio_unit[]"]').value =
        precio ? parseFloat(precio).toFixed(2) : '';
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const cant   = parseFloat(row.querySelector('input[name="cantidad[]"]').value) || 0;
        const precio = parseFloat(row.querySelector('input[name="precio_unit[]"]').value) || 0;
        total += cant * precio;
    });
    document.getElementById('totalDisplay').textContent = 'S/ ' + total.toFixed(2);
    document.getElementById('totalInput').value = total.toFixed(2);
}

function verDetalle(id) {
    document.getElementById('detalleSubtitle').textContent = 'Cargando...';
    document.getElementById('detalleContenido').innerHTML =
        '<p style="color:#aaa;text-align:center;padding:20px">Cargando...</p>';
    abrirModal('modalDetalle');
    fetch(`../../controllers/VentaDetalleController.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('detalleSubtitle').textContent =
                `Venta #${data.venta.id} — ${data.venta.fecha}`;
            let html = '<table style="width:100%;border-collapse:collapse;font-size:13px">';
            html += '<thead><tr style="border-bottom:1px solid #e8edf2">'
                  + '<th style="text-align:left;padding:6px 0;color:#aaa;font-size:11px;text-transform:uppercase">Producto</th>'
                  + '<th style="text-align:center;padding:6px 0;color:#aaa;font-size:11px;text-transform:uppercase">Cant.</th>'
                  + '<th style="text-align:right;padding:6px 0;color:#aaa;font-size:11px;text-transform:uppercase">Subtotal</th>'
                  + '</tr></thead><tbody>';
            data.detalles.forEach(d => {
                html += `<tr style="border-bottom:1px solid #f0f4f8">
                    <td style="padding:9px 0">${d.producto}</td>
                    <td style="text-align:center;padding:9px 0">${d.cantidad}</td>
                    <td style="text-align:right;padding:9px 0">S/ ${parseFloat(d.subtotal).toFixed(2)}</td>
                </tr>`;
            });
            html += `</tbody><tfoot><tr>
                <td colspan="2" style="padding-top:10px;font-weight:600">Total</td>
                <td style="text-align:right;padding-top:10px;font-weight:700;color:#145c34;font-size:16px">
                    S/ ${parseFloat(data.venta.total).toFixed(2)}
                </td></tr></tfoot></table>`;
            document.getElementById('detalleContenido').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('detalleContenido').innerHTML =
                '<p style="color:#c0392b;text-align:center">Error al cargar el detalle.</p>';
        });
}

function toggleFiltro(tipo, btn) {
    document.getElementById('hiddenFiltro').value = tipo;
    document.getElementById('formFiltro').style.display = 'block';
    document.getElementById('inputDia').style.display = tipo==='dia' ? 'flex' : 'none';
    document.getElementById('inputMes').style.display = tipo==='mes' ? 'flex' : 'none';
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}


</script>

</body>
</html>