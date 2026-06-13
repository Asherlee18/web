<?php
date_default_timezone_set('America/Lima');

$conexion = new mysqli("localhost", "root", "", "jugueria_aitana");
if ($conexion->connect_error) {
    die("Error de conexión");
}