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
public function viewProductionPeriod() {
    // Obtener el último periodo quincenal
    $last_biweekly = DB::table('biweekly')->latest('biweekly_id')->first();

    // Primero obtenemos los datos diarios de producción para cada empleado
    $daily_production = DB::table('activity_log')
        ->join('employees', 'activity_log.employee_id', '=', 'employees.employee_id')
        ->join('products_production_stages', 'activity_log.production_stages_id', '=', 'products_production_stages.production_stages_id')
        ->select(
            'employees.employee_id',
            'employees.name',
            'employees.last_name_pather',
            'employees.last_name_mother',
            'activity_log.date_production',
            DB::raw('SUM(activity_log.quantity_produced) as daily_produced'),
            DB::raw('SUM(products_production_stages.quantity_to_produce) as daily_goal'),
            'products_production_stages.stage_types_id'
        )
        ->where('activity_log.biweekly_id', $last_biweekly->biweekly_id)
        ->groupBy('employees.employee_id', 'employees.name', 'employees.last_name_pather', 
                 'employees.last_name_mother', 'activity_log.date_production', 
                 'products_production_stages.stage_types_id')
        ->get();

    // Inicializamos un array para almacenar los resultados finales
    $final_salaries = [];

    // Procesamos la producción diaria para cada empleado
    foreach ($daily_production as $record) {
        $employee_id = $record->employee_id;
        $date = $record->date_production;
        
        // Inicializamos el empleado si no existe en el array final
        if (!isset($final_salaries[$employee_id])) {
            $final_salaries[$employee_id] = (object)[
                'employee_id' => $record->employee_id,
                'name' => $record->name,
                'last_name_pather' => $record->last_name_pather,
                'last_name_mother' => $record->last_name_mother,
                'total_quantity_produced' => 0,
                'total_quantity_to_produce' => 0,
                'days_worked' => 0,
                'rest_days' => 0,
                'daily_salaries' => [],
                'daily_details' => [], // Para guardar detalles de cada día
                'final_salary' => 0,
                'absences' => 0,
                'worked_days_dates' => [],
                'rest_days_dates' => []
            ];
        }

        // Si es un día de descanso (stage_types_id = 100005)
        if ($record->stage_types_id == '100005') {
            if (!isset($final_salaries[$employee_id]->rest_days_dates[$date])) {
                $final_salaries[$employee_id]->rest_days++;
                $final_salaries[$employee_id]->rest_days_dates[$date] = true;
                
                // Asignamos el pago completo para el día de descanso (100% del salario base)
                $rest_day_salary = $last_biweekly->wage_by_day;
                
                // Guardamos el salario y los detalles del día de descanso
                $final_salaries[$employee_id]->daily_salaries[$date] = $rest_day_salary;
                $final_salaries[$employee_id]->daily_details[$date] = [
                    [
                        'date' => $date,
                        'type' => 'rest_day',
                        'salary' => $rest_day_salary,
                        'description' => 'Día de descanso'
                    ]
                ];
            }
            continue;
        }

        // Acumulamos las cantidades totales
        $final_salaries[$employee_id]->total_quantity_produced += $record->daily_produced;
        $final_salaries[$employee_id]->total_quantity_to_produce += $record->daily_goal;

        // Contamos el día como trabajado si no lo hemos contado antes
        if (!isset($final_salaries[$employee_id]->worked_days_dates[$date])) {
            $final_salaries[$employee_id]->days_worked++;
            $final_salaries[$employee_id]->worked_days_dates[$date] = true;
        }

        // Calculamos el factor de cumplimiento diario (producido / objetivo)
        // Ejemplo: Si el objetivo es 55 y produce 55, entonces 55/55 = 1
        $daily_factor = 0;
        if ($record->daily_goal > 0) {
            $daily_factor = $record->daily_produced / $record->daily_goal;
            // Opcional: si quieres limitar el factor a máximo 1 (100%)
            // $daily_factor = min($daily_factor, 1);
        }
        
        // Calculamos el salario diario multiplicando el factor por el sueldo base
        // Ejemplo: 1 * sueldo_base = sueldo_base (100% del sueldo)
        $daily_salary = $daily_factor * $last_biweekly->wage_by_day;
        
        // Guardamos los detalles del cálculo para este día
        $daily_detail = [
            'date' => $date,
            'type' => 'work_day',
            'produced' => $record->daily_produced,
            'goal' => $record->daily_goal,
            'factor' => $daily_factor,
            'salary' => $daily_salary,
            'description' => 'Día de trabajo'
        ];
        
        // Agregamos o actualizamos el salario y detalles diarios
        if (!isset($final_salaries[$employee_id]->daily_salaries[$date])) {
            $final_salaries[$employee_id]->daily_salaries[$date] = $daily_salary;
            $final_salaries[$employee_id]->daily_details[$date] = [$daily_detail];
        } else {
            $final_salaries[$employee_id]->daily_salaries[$date] += $daily_salary;
            $final_salaries[$employee_id]->daily_details[$date][] = $daily_detail;
        }
    }

    // Calculamos el salario final y las ausencias para cada empleado
    foreach ($final_salaries as $employee_id => $employee) {
        // Sumamos todos los salarios diarios para obtener el total quincenal
        $employee->final_salary = array_sum($employee->daily_salaries);
        
        // Calculamos las ausencias
        $employee->absences = $last_biweekly->day_for_biweekly - ($employee->days_worked + $employee->rest_days);
        
        // Ordenamos los detalles diarios por fecha
        ksort($employee->daily_details);
        
        // Limpiamos las propiedades auxiliares que ya no necesitamos
        unset($employee->worked_days_dates);
        unset($employee->rest_days_dates);
    }

    // Convertimos el arreglo a una colección para pasarlo a la vista
    $final_salaries = collect(array_values($final_salaries));

    return view("final_salary_payment_report", compact('final_salaries', 'last_biweekly'));
}




    public function GeneratePDF(){

    }
    
    
    
    
}
