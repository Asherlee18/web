<?php
session_start();
require_once("../config/database.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    $sql = "SELECT * FROM usuarios
            WHERE usuario='$usuario'
            AND clave='$clave'";

    $resultado = $conexion->query($sql);

    if($resultado->num_rows > 0){

        $fila = $resultado->fetch_assoc();

        $_SESSION["id"] = $fila["id"];
        $_SESSION["nombre"] = $fila["nombre"];
        $_SESSION["rol"] = $fila["rol"];

        header("Location: ../views/dashboard/index.php");
        exit();
    }

    header("Location: ../views/login/login.php?error=1");
}