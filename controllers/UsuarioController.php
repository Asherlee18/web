<?php
require_once __DIR__ . "/../config/database.php";

$nombre  = $_POST["nombre"]  ?? '';
$usuario = $_POST["usuario"] ?? '';
$clave   = password_hash($_POST["clave"] ?? '', PASSWORD_BCRYPT);
$rol     = $_POST["rol"]     ?? 'mesera';

$stmt = $conexion->prepare(
    "INSERT INTO usuarios(nombre, usuario, clave, rol) VALUES(?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $nombre, $usuario, $clave, $rol);
$stmt->execute();

header("Location: ../views/usuarios/index.php");
exit();
