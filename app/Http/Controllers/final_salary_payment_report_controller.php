<?php

namespace App\Http\Controllers;

use App\Models\product_production_stage;
use Illuminate\Http\Request;
use App\Models\activity_log;
use Illuminate\Support\Facades\DB;
use App\Models\employee;
use App\Models\biweekly;
use Carbon\Carbon;
use PDF;

class final_salary_payment_report_controller extends Controller
{   
public function viewProductionPeriod() {
    // Obtener el último periodo quincenal
    $lastBiweekly = Biweekly::orderBy('start_date', 'desc')->first();
    $lastBiweeklyId = $lastBiweekly->biweekly_id;

    // Obtener los registros de actividad con relaciones cargadas
    $activityLogs = activity_log::with(['employee', 'products_production_stage'])
        ->where('biweekly_id', $lastBiweeklyId)
        ->orderBy('employee_id')
        ->get();

    $finalSalaries = [];

    foreach ($activityLogs as $log) {
        $employeeId = $log->employee->employee_id;
        $date = $log->date_production;

        if (!isset($finalSalaries[$employeeId])) {
            $finalSalaries[$employeeId] = (object)[
                'employee_id' => $log->employee->employee_id,
                'name' => $log->employee->name,
                'last_name_pather' => $log->employee->last_name_pather,
                'last_name_mother' => $log->employee->last_name_mother,
                'total_quantity_produced' => 0,
                'total_quantity_to_produce' => 0,
                'days_worked' => 0,
                'rest_days' => 0,
                'daily_salaries' => [],
                'daily_details' => [],
                'final_salary' => 0,
                'absences' => 0,
                'worked_days_dates' => [],
                'rest_days_dates' => []
            ];
        }

        $stageType = $log->products_production_stage->stage_types_id;
        $quantityToProduce = $log->products_production_stage->quantity_to_produce;
        $quantityProduced = $log->quantity_produced;

        if ($stageType == '100005') {
            if (!isset($finalSalaries[$employeeId]->rest_days_dates[$date])) {
                $finalSalaries[$employeeId]->rest_days++;
                $finalSalaries[$employeeId]->rest_days_dates[$date] = true;

                $restDaySalary = $lastBiweekly->wage_by_day;
                $finalSalaries[$employeeId]->daily_salaries[$date] = $restDaySalary;
                $finalSalaries[$employeeId]->daily_details[$date] = [
                    [
                        'date' => $date,
                        'type' => 'rest_day',
                        'salary' => $restDaySalary,
                        'description' => 'Día de descanso'
                    ]
                ];
            }
            continue;
        }

        $finalSalaries[$employeeId]->total_quantity_produced += $quantityProduced;
        $finalSalaries[$employeeId]->total_quantity_to_produce += $quantityToProduce;

        if (!isset($finalSalaries[$employeeId]->worked_days_dates[$date])) {
            $finalSalaries[$employeeId]->days_worked++;
            $finalSalaries[$employeeId]->worked_days_dates[$date] = true;
        }

        $dailyFactor = $quantityToProduce > 0 ? $quantityProduced / $quantityToProduce : 0;
        $dailySalary = $dailyFactor * $lastBiweekly->wage_by_day;

        $dailyDetail = [
            'date' => $date,
            'type' => 'work_day',
            'produced' => $quantityProduced,
            'goal' => $quantityToProduce,
            'factor' => $dailyFactor,
            'salary' => $dailySalary,
            'description' => 'Día de trabajo'
        ];

        if (!isset($finalSalaries[$employeeId]->daily_salaries[$date])) {
            $finalSalaries[$employeeId]->daily_salaries[$date] = $dailySalary;
            $finalSalaries[$employeeId]->daily_details[$date] = [$dailyDetail];
        } else {
            $finalSalaries[$employeeId]->daily_salaries[$date] += $dailySalary;
            $finalSalaries[$employeeId]->daily_details[$date][] = $dailyDetail;
        }
    }

    // Cálculo final por empleado
    foreach ($finalSalaries as $employee) {
        $employee->final_salary = array_sum($employee->daily_salaries);
        $employee->absences = $lastBiweekly->day_for_biweekly - ($employee->days_worked + $employee->rest_days);
        ksort($employee->daily_details);
        unset($employee->worked_days_dates, $employee->rest_days_dates);
    }

    $finalSalaries = collect(array_values($finalSalaries));

    return view("final_salary_payment_report", compact('finalSalaries', 'lastBiweekly'));
}





public function GeneratePDF(){ 
    // Obtener el último biweekly_id basado en start_date (o payment_date)
    $lastBiweekly = Biweekly::orderBy('start_date', 'desc')->first();
    $lastBiweeklyId = $lastBiweekly->biweekly_id;

    // Obtener las fechas de inicio y fin del periodo
    $startDate = $lastBiweekly->start_date;
    $endDate = $lastBiweekly->end_date;

    // Asegurarse de que las fechas sean instancias de Carbon
    $startDateFormatted = \Carbon\Carbon::parse($startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::parse($endDate)->format('Y-m-d');

    // Filtrar los registros de activity_log con ese biweekly_id
    $viewDatailsSalary = activity_log::with(['employee', 'biweekly', 'products_production_stage'])
        ->where('biweekly_id', $lastBiweeklyId)
        ->orderBy('employee_id') // Opcional: ordenar por employee_id
        ->get();

    // Generar el nombre del archivo con las fechas
    $fileName = "reporte_del_sueldo_{$startDateFormatted}_a_{$endDateFormatted}.pdf";

    // Generar el PDF y descargarlo con el nombre dinámico
    $pdf = PDF::loadView('layouts.payment_details', compact('viewDatailsSalary'));
    return $pdf->download($fileName);
}


}
