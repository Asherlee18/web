<?php
require_once __DIR__ . "/../config/database.php";

$descripcion = $_POST["descripcion"] ?? '';
$monto       = $_POST["monto"] ?? 0;

$stmt = $conexion->prepare("INSERT INTO gastos(descripcion, monto, fecha) VALUES(?, ?, CURDATE())");
$stmt->bind_param("sd", $descripcion, $monto);
$stmt->execute();

header("Location: ../views/gastos/listar.php");
exit();
