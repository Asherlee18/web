<?php

require_once __DIR__ . "/../models/venta.php";
require_once __DIR__ . "/../models/gasto.php";

class DashboardController {

    public function resumen(){

        $venta = new Venta();
        $gasto = new Gasto();

        return [
            'ventas' => $venta->totalVentas(),
            'gastos' => $gasto->totalGastos()
        ];

    }

}