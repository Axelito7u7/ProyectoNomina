<?php

namespace App\Http\Controllers;

use App\Models\product_production_stage;
use Illuminate\Http\Request;
use App\Models\Activity_log;
use Illuminate\Support\Facades\DB;
use App\Models\biweekly;
use Carbon\Carbon;
class final_salary_payment_report_controller extends Controller
{   
    public function View_final_salary() {
    $last_biweely = Biweekly::orderBy('biweekly_id', 'desc')->first();

    // Obtener todos los empleados que tienen actividad en esa quincena
    $View_final_salary = DB::table('wages')
        ->join('activity_log', 'wages.activity_log_id', '=', 'activity_log.activity_log_id')
        ->join('employees', 'activity_log.employee_id', '=', 'employees.employee_id')
        ->join('products_production_stages', 'activity_log.production_stages_id', '=', 'products_production_stages.production_stages_id')
        ->join('biweekly', 'wages.biweekly_id', '=', 'biweekly.biweekly_id')
        ->select(
            'employees.employee_id',
            'employees.name as name', 
            'employees.last_name_pather as last_name_father', 
            'employees.last_name_mother as last_name_mother',

            // Días realmente trabajados (excluye días de descanso)
            DB::raw("COUNT(DISTINCT CASE WHEN products_production_stages.stage_types_id != '100005' THEN DATE(wages.processing_date) END) as days_worked"),

            // Total de días registrados para el empleado (incluso descansos)
            DB::raw("COUNT(DISTINCT DATE(wages.processing_date)) as total_registered_days"),

            DB::raw('SUM(wages.pay_by_day_and_number) as total_salary')
        )
        ->where('wages.biweekly_id', $last_biweely->biweekly_id)
        ->groupBy(
            'employees.employee_id',
            'employees.name',
            'employees.last_name_pather',
            'employees.last_name_mother'
        )
        ->get();

    // Calcular ausencias por diferencia de días esperados vs. trabajados
    foreach ($View_final_salary as $employee) {
        // Se considera ausencia si no hay registro en algún día de la quincena
        $employee->absences = $employee->total_registered_days - $employee->days_worked;
    }

    return view("final_salary_payment_report", compact('View_final_salary', 'last_biweely'));
}

    public function GeneratePDF(){

    }
    
    
    
    
}
