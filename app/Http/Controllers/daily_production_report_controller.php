<?php

namespace App\Http\Controllers;
use App\Models\biweekly;
use App\Models\employee;
use App\Models\product_production_stage;//Importacion del modelo
use App\Models\activity_log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Symfony\Component\Mailer\Event\MessageEvent;

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

    public function Add_daily_production_report(Request $request){
        for ($i = 0; $i < count($request->empleado_id); $i++) {
            DB::table('activity_log')->insert([
                'production_stages_id' => $request -> actividad[$i],
                'employee_id'=> $request -> empleado_id[$i],
                'biweekly_id'=> $request -> id_fecha_procesamiento,
                'date_production'=> $request -> fechaActual,
                'quantity_produced'=> $request -> produccion[$i],
            ]);
        }
        return redirect('/')->with('success', 'Producción guardada correctamente.');
        // return $request;
    }
}
