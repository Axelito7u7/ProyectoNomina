<?php

namespace App\Http\Controllers;

use App\Models\Activity_log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class production_period_controller extends Controller
{
    public function viewProductionPeriod(){    
            $query = DB::table('activity_log')
                ->join('employees', 'activity_log.employee_id', '=', 'employees.employee_id')
                ->join('products_production_stages', 'activity_log.production_stages_id', '=', 'products_production_stages.production_stages_id')
                ->join('biweekly', 'activity_log.biweekly_id', '=', 'biweekly.biweekly_id')
                ->select('activity_log.*', 
                    'employees.name as userName', 
                    'employees.last_name_pather as userPather', 
                    'employees.last_name_mother as userMother', 
                    'products_production_stages.name as product_name',
                    'products_production_stages.quantity_to_produce as quantity_produce',
                    'biweekly.wage_by_day as wage_day')

            ->get();


        $date = Carbon::now();

        return view("production_period", compact("date", 'query'));
    }


    public function save(Request $request){
        $request->validate([
            'quantity_produced' => 'numeric|max:999|required',
        ]);

        Activity_log::where('activity_log_id', $request->activity_log_id)
        ->update(['quantity_produced' => $request->quantity_produced]);


        return redirect()->back()->with('success', 'Producción guardada');
    }
}
