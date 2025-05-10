<?php

namespace App\Http\Controllers;

use App\Models\Activity_log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Notifications\Action;

class production_period_controller extends Controller
{
    public function viewProductionPeriod(){    

        //consultas
            $query = DB::table('activity_log')
                ->join('employees', 'activity_log.employee_id', '=', 'employees.employee_id')
                ->join('products_production_stages', 'activity_log.production_stages_id', '=', 'products_production_stages.production_stages_id')
                ->join('biweekly', 'activity_log.biweekly_id', '=', 'biweekly.biweekly_id')
                ->select('activity_log.*', 
                    'activity_log.employee_id as employee_id', 
                    'employees.name as userName',
                    'employees.last_name_pather as userPather', 
                    'employees.last_name_mother as userMother', 
                    'products_production_stages.name as product_name',
                    'products_production_stages.quantity_to_produce as quantity_to_produced',
                    'biweekly.wage_by_day as wage_day',
                    'activity_log.quantity_produced as quantity_produce')
                ->get();


            $dates = DB::table('biweekly')
                ->orderBy('start_date', 'desc')
                ->orderBy('end_date', 'desc')
                -> select('start_date', 'end_date', 'wage_by_day')
                ->first();


            $employees = DB::table('employees')
                ->orderBy('name', 'asc')
                ->select('*')
                ->get();


                
            //funcion carbon para obtener fechas y convertira fechas 
                $startDate = Carbon::parse($dates -> start_date);
                $endDate = Carbon::parse($dates -> end_date);
                $days_period = $startDate -> diffInDays($endDate) + 1;
                $date = Carbon::now();


                    if ($date <= $endDate) {
                        for ($i = 0; $i < $days_period; $i++);
                    }
                    
            //operacion para calcular sueldo                        
            foreach($query as $querys){
                $quantity = $querys->quantity_produce;
                $obj = $querys->quantity_to_produced;
                $wage = $querys->wage_day;

                $end_wage = ($wage / $obj) * $quantity;

                // Agregamos el valor calculado al objeto
                $querys->end_wage = $end_wage;
            }



        return view("production_period", compact("date", 'query', 'dates', 'startDate', 'endDate', 'i', 'employees', 'end_wage'));
    }




    public function save(Request $request){
        for ($i = 0; $i < count($request->id_pp); $i++) {
            DB::table('activity_log')
            -> where(['activity_log_id' => $request -> id_pp[$i]])
            ->update(['quantity_produced' => $request -> quantity_produced[$i],
        ]);
        };

       return redirect()->back()->with('success', 'Producción guardada', );
       //return $request;
    }
}
