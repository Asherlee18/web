<?php
require_once("../config/database.php");

$id = $_GET["id"];

$sql = "SELECT * FROM ventas WHERE id=$id";
$resultado = $conexion->query($sql);

$venta = $resultado->fetch_assoc();