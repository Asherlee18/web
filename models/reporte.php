<?php

require_once __DIR__ . "/../config/database.php";

class Reporte {

    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function diario() {

        return $this->conexion->query(
            "SELECT *
             FROM ventas
             WHERE DATE(fecha)=CURDATE()"
        );
    }

    public function semanal() {

        return $this->conexion->query(
            "SELECT *
             FROM ventas
             WHERE YEARWEEK(fecha)=YEARWEEK(NOW())"
        );
    }

    public function mensual() {

        return $this->conexion->query(
            "SELECT *
             FROM ventas
             WHERE MONTH(fecha)=MONTH(NOW())
             AND YEAR(fecha)=YEAR(NOW())"
        );
    }

}