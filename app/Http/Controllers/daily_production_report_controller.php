<?php

namespace App\Http\Controllers;
use App\Models\product_production_stage;//Importacion del modelo
use Illuminate\Http\Request;

class daily_production_report_controller extends Controller
{
    public function index(){
        $Etapas_producion = product_production_stage::all();//mostrarr todo los datos
        return view("daily_production_report", compact("Etapas_producion"));//compact se usa para indicar que se va mandar a la vista
    }
}
