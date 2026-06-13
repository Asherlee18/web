<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/venta.php";

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['error' => 'ID inválido']); exit; }

$ventaModel = new Venta();
$detalles_result = $ventaModel->detalles($id);

$stmt = $conexion->prepare("SELECT * FROM ventas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();
$venta['fecha'] = date('d/m/Y H:i', strtotime($venta['fecha']));

$detalles = [];
while ($d = $detalles_result->fetch_assoc()) $detalles[] = $d;

echo json_encode(['venta' => $venta, 'detalles' => $detalles]);
