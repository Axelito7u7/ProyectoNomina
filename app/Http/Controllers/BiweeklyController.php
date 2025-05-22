<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class BiweeklyController extends Controller
{
    /**
     * Mostrar lista de períodos quincenales
     */
    public function index()
    {
        $biweeklies = DB::table('biweekly')
            ->orderBy('start_date', 'desc')
            ->get();

        foreach ($biweeklies as $biweekly) {
            $startDate = Carbon::parse($biweekly->start_date);
            $endDate = Carbon::parse($biweekly->end_date);
            $biweekly->days_count = $startDate->diffInDays($endDate) + 1;

            // Determine status based on whether the payment_date has passed
            $biweekly->status = Carbon::parse($biweekly->payment_date)->isPast() ? 'Cerrado' : 'Activo';

            $totalWagesForBiweekly = 0;

            // If the period is 'Cerrado' (payment_date has passed), calculate the total wages for display.
            if ($biweekly->status === 'Cerrado') {
                // Fetch all employees and their activities for this biweekly period
                $employees = DB::table('employees')
                    ->select('employee_id')
                    ->get();

                foreach ($employees as $employee) {
                    $activities = DB::table('activity_log')
                        ->join('products_production_stages', 'activity_log.production_stages_id', '=', 'products_production_stages.production_stages_id')
                        ->select(
                            'activity_log.quantity_produced',
                            'products_production_stages.quantity_to_produce'
                        )
                        ->where('activity_log.employee_id', $employee->employee_id)
                        ->where('activity_log.biweekly_id', $biweekly->biweekly_id)
                        ->get();

                    $employeeTotalWage = 0;
                    foreach ($activities as $activity) {
                        if ($activity->quantity_to_produce > 0) {
                            $calculatedWage = ($biweekly->wage_by_day / $activity->quantity_to_produce) * $activity->quantity_produced;
                            $employeeTotalWage += $calculatedWage;
                        }
                    }
                    $totalWagesForBiweekly += $employeeTotalWage;
                }
            }
            // total_wages is calculated on the fly and not stored in any table.
            $biweekly->total_wages = $totalWagesForBiweekly;
        }

        return view('admin.biweekly', compact('biweeklies'));
    }

    /**
     * Mostrar formulario para crear un nuevo período quincenal
     */
    public function create()
    {
        return view('admin.biweekly');
    }

    /**
     * Almacenar un nuevo período quincenal
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'payment_date' => 'required|date|after_or_equal:end_date',
            'day_for_biweekly' => 'required|integer|min:1',
            'wage_by_day' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_modal', 'create');
        }

        try {
            DB::table('biweekly')->insert([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'payment_date' => $request->payment_date,
                'day_for_biweekly' => $request->day_for_biweekly,
                'wage_by_day' => $request->wage_by_day,
                // Removed 'created_at' and 'updated_at' as per your table schema
            ]);

            return redirect()->route('biweekly.index')->with('success', 'Período quincenal creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear período quincenal: ' . $e->getMessage())
                ->withInput()
                ->with('error_modal', 'create');
        }
    }

    /**
     * Mostrar información de un período quincenal específico
     */
    public function show($id)
    {
        $biweekly = DB::table('biweekly')
            ->where('biweekly_id', $id)
            ->first();

        if (!$biweekly) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Período quincenal no encontrado'], 404);
            }
            return redirect()->route('biweekly.index')->with('error', 'Período quincenal no encontrado');
        }

        $startDate = Carbon::parse($biweekly->start_date);
        $endDate = Carbon::parse($biweekly->end_date);
        $biweekly->days_count = $startDate->diffInDays($endDate) + 1;

        // Determine status for the single biweekly period being viewed based on payment_date
        $biweekly->status = Carbon::parse($biweekly->payment_date)->isPast() ? 'Cerrado' : 'Activo';

        $employees = DB::table('employees')
            ->select('employee_id', 'name', 'middle_name', 'last_name_pather', 'last_name_mother')
            ->orderBy('name')
            ->get();

        foreach ($employees as $employee) {
            $totalQuantityProduced = DB::table('activity_log')
                ->where('employee_id', $employee->employee_id)
                ->where('biweekly_id', $id)
                ->sum('quantity_produced');

            $employee->total_quantity_produced = $totalQuantityProduced;

            $employeeTotalWage = 0;
            $activities = DB::table('activity_log')
                ->join('products_production_stages', 'activity_log.production_stages_id', '=', 'products_production_stages.production_stages_id')
                ->select(
                    'activity_log.quantity_produced',
                    'products_production_stages.quantity_to_produce'
                )
                ->where('activity_log.employee_id', $employee->employee_id)
                ->where('activity_log.biweekly_id', $id)
                ->get();

            foreach ($activities as $activity) {
                if ($activity->quantity_to_produce > 0) {
                    $calculatedWage = ($biweekly->wage_by_day / $activity->quantity_to_produce) * $activity->quantity_produced;
                    $employeeTotalWage += $calculatedWage;
                }
            }
            // total_wages_paid is calculated on the fly
            $employee->total_wages_paid = $employeeTotalWage;
        }

        // Calculate total wages for the biweekly period for display in modal
        $biweekly->total_wages = 0; // Initialize
        foreach ($employees as $employee) {
            $biweekly->total_wages += $employee->total_wages_paid; // Sum up calculated individual wages
        }

        if (request()->ajax()) {
            return response()->json(compact('biweekly', 'employees'));
        }

        return view('admin.biweekly_details', compact('biweekly', 'employees'));
    }

    /**
     * Obtener datos de un período quincenal para editar
     */
    public function edit($id)
    {
        $biweekly = DB::table('biweekly')
            ->where('biweekly_id', $id)
            ->first();

        if (!$biweekly) {
            return response()->json(['error' => 'Período quincenal no encontrado'], 404);
        }

        return response()->json($biweekly);
    }

    /**
     * Actualizar un período quincenal
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'payment_date' => 'required|date|after_or_equal:end_date',
            'day_for_biweekly' => 'required|integer|min:1',
            'wage_by_day' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_modal', 'edit')
                ->with('edit_id', $id);
        }

        try {
            DB::table('biweekly')
                ->where('biweekly_id', $id)
                ->update([
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'payment_date' => $request->payment_date,
                    'day_for_biweekly' => $request->day_for_biweekly,
                    'wage_by_day' => $request->wage_by_day,
                    // Removed 'updated_at' as per your table schema
                ]);

            return redirect()->route('biweekly.index')->with('success', 'Período quincenal actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar período quincenal: ' . $e->getMessage())
                ->withInput()
                ->with('error_modal', 'edit')
                ->with('edit_id', $id);
        }
    }

    /**
     * Cerrar período quincenal. This action will now confirm the logical closure based on payment_date.
     */
    public function close(Request $request, $id)
    {
        try {
            $biweekly = DB::table('biweekly')
                ->where('biweekly_id', $id)
                ->first();

            if (!$biweekly) {
                return redirect()->back()->with('error', 'Período quincenal no encontrado.');
            }

            // If the payment date has already passed, the period is logically "Cerrado".
            if (Carbon::parse($biweekly->payment_date)->isPast()) {
                return redirect()->back()->with('error', 'Este período ya está cerrado (la fecha de pago ha pasado).');
            }

            // If the payment date has not passed, "closing" means acknowledging it will be closed on its payment date.
            // No database change is needed for 'status' or a 'wages' table for this action.
            return redirect()->route('biweekly.index')->with('success', 'Período marcado para cierre automático al pasar la fecha de pago.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar cerrar período: ' . $e->getMessage());
        }
    }
}