<?php

namespace App\Http\Controllers;
use App\Models\biweekly;
use App\Models\employee;
use App\Models\product_production_stage;//Importacion del modelo
use Illuminate\Http\Request;

class daily_production_report_controller extends Controller
{
    //funcion para mostrar datos
    public function View_daily_production_report(){
        $View_product_production = product_production_stage::all();//mostrarr todo los datos
        $View_Employees = employee::all();
        $View_biweekly = biweekly::all();

        return view("daily_production_report", compact("View_product_production", "View_Employees", "View_biweekly"));//compact se usa para indicar que se va mandar a la vista
    }
}
