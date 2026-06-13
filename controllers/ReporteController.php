<?php

require_once __DIR__ . "/../models/venta.php";
require_once __DIR__ . "/../models/gasto.php";

class ReporteController {

    public function reporteDiario(){

        $venta = new Venta();

        return $venta->reporteDiario();

    }

    public function reporteSemanal(){

        $venta = new Venta();

        return $venta->reporteSemanal();

    }

    public function reporteMensual(){

        $venta = new Venta();

        return $venta->reporteMensual();

    }

}