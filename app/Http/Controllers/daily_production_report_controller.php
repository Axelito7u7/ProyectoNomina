<?php

namespace App\Http\Controllers;
use App\Models\biweekly;
use App\Models\employee;
use App\Models\product_production_stage;//Importacion del modelo
use App\Models\activity_log;
use Illuminate\Http\Request;

class daily_production_report_controller extends Controller
{
    //funcion para mostrar datos
    public function View_daily_production_report(){
        //  $viewProductProduction = ProductProductionStage::with(['employee', 'biweekly'])->get();  // vasicamente es un join

        $View_product_production = product_production_stage::all();//mostrarr todo los datos
        $View_Employees = employee::all();
        //consulta donde ordenamos id de biweekly de manera descedente y solo mandaremos el primer resultado con first
        $View_biweekly = Biweekly::orderBy('biweekly_id', 'desc')->first();
        $View_activity_log = activity_log::all(); 

        return view("daily_production_report", compact("View_product_production", "View_Employees", "View_biweekly","View_activity_log"));//compact se usa para indicar que se va mandar a la vista
    }

    public function Insert_daily_production_report(){
        
    }
}
