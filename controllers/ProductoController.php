<?php
require_once __DIR__ . "/../config/database.php";

if (isset($_POST["guardar"])) {
    $nombre = $_POST["nombre"] ?? '';
    $precio = $_POST["precio"] ?? 0;
    $stock  = $_POST["stock"] ?? 0;

    $stmt = $conexion->prepare("INSERT INTO productos(nombre, precio, stock) VALUES(?, ?, ?)");
    $stmt->bind_param("sdi", $nombre, $precio, $stock);
    $stmt->execute();

    header("Location: ../views/productos/listar.php");
    exit();
}
