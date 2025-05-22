<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Mostrar lista de empleados
     */
    public function index()
    {
        $employees = DB::table('employees')
            ->select('employee_id', 'name', 'middle_name', 'last_name_pather', 'last_name_mother', 'fire_date') // Explicitly select columns
            ->orderBy('name')
            ->get();
            
        // Removed addressTypes as it's not relevant to simplified employee table for index view
        // The blade file might still expect it if other parts of the menu/layout depend on it,
        // but for a simplified employee table, it's not directly needed here.
        // If addressTypes are used elsewhere in 'layouts.menu' or other parts of the view,
        // you might need to keep fetching it or adjust those parts.

        return view('admin.employee', compact('employees')); // Removed 'addressTypes' from compact
    }
    
    /**
     * Método alternativo para listar empleados (API o formato diferente)
     */
    public function list()
    {
        $employees = DB::table('employees')
            ->select('employee_id', 'name', 'middle_name', 'last_name_pather', 'last_name_mother', 'fire_date')
            ->orderBy('name')
            ->get();
            
        // Formatear datos para API o vista alternativa
        foreach ($employees as $employee) {
            $employee->full_name = $employee->name . ' ' . $employee->last_name_pather . ' ' . $employee->last_name_mother;
            // Check if fire_date is not null before formatting
            $employee->fire_date_formatted = $employee->fire_date ? Carbon::parse($employee->fire_date)->format('d/m/Y') : 'N/A';
        }

        // Si es una solicitud AJAX, devolver JSON
        if (request()->ajax()) {
            return response()->json($employees);
        }
        
        // De lo contrario, devolver una vista
        return view('admin.employee', compact('employees'));
    }

    /**
     * Mostrar formulario para crear un nuevo empleado
     */
    public function create()
    {
        // Removed Employee::all() and AddressType::all() as they are not needed for a simple modal-based create form
        // and would require importing models.
        // If this method is not actually used to render a dedicated create page, but just to satisfy a route,
        // you can return an empty view or redirect.
        return view('admin.employee'); // Or return redirect()->route('employees.index');
    }


    /**
     * Almacenar un nuevo empleado
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name_pather' => 'required|string|max:255',
            'last_name_mother' => 'required|string|max:255',
            'fire_date' => 'required|date', // Keep fire_date as per your table
            // Removed all address-related fields from validation
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_modal', 'create');
        }

        try {
            DB::table('employees')->insert([
                'name' => $request->name,
                'middle_name' => $request->middle_name,
                'last_name_pather' => $request->last_name_pather,
                'last_name_mother' => $request->last_name_mother,
                'fire_date' => $request->fire_date, // Insert fire_date
                // Removed 'created_at' and 'updated_at' if they don't exist in your table.
                // Removed all address-related fields from insertion
            ]);

return redirect()->route('employee.index')->with('success', 'Empleado creado exitosamente');        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear empleado: ' . $e->getMessage())
                ->withInput()
                ->with('error_modal', 'create');
        }
    }

    /**
     * Mostrar información de un empleado
     */
    public function show($id)
    {
        $employee = DB::table('employees')
            // Removed join with address_type as it's not in your simplified schema
            ->select('employee_id', 'name', 'middle_name', 'last_name_pather', 'last_name_mother', 'fire_date') // Select only relevant columns
            ->where('employee_id', $id)
            ->first();

        if (!$employee) {
            // Changed to return JSON for AJAX requests, as the front-end fetch expects it.
            if (request()->ajax()) {
                 return response()->json(['error' => 'Empleado no encontrado'], 404);
            }
            return redirect()->route('employees.index')->with('error', 'Empleado no encontrado');
        }

        // Removed activities and biweeklies as they are not directly related to the simplified employee table.
        // If you need these, they should be fetched from other tables and likely require separate logic.

        // If it's an AJAX request, return JSON. Otherwise, return the view.
        if (request()->ajax()) {
            return response()->json($employee);
        }
        
        return view('admin.employee', compact('employee')); // Pass only the employee
    }

    /**
     * Obtener datos de un empleado para editar
     */
    public function edit($id)
    {
        $employee = DB::table('employees')
            ->select('employee_id', 'name', 'middle_name', 'last_name_pather', 'last_name_mother', 'fire_date') // Select only relevant columns
            ->where('employee_id', $id)
            ->first();
            
        if (!$employee) {
            return response()->json(['error' => 'Empleado no encontrado'], 404);
        }
        
        return response()->json($employee);
    }

    /**
     * Actualizar un empleado
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name_pather' => 'required|string|max:255',
            'last_name_mother' => 'required|string|max:255',
            'fire_date' => 'required|date', // Keep fire_date
            // Removed all address-related fields from validation
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_modal', 'edit')
                ->with('edit_id', $id);
        }

        try {
            DB::table('employees')
                ->where('employee_id', $id)
                ->update([
                    'name' => $request->name,
                    'middle_name' => $request->middle_name,
                    'last_name_pather' => $request->last_name_pather,
                    'last_name_mother' => $request->last_name_mother,
                    'fire_date' => $request->fire_date, // Update fire_date
                    // Removed 'updated_at' if it doesn't exist in your table.
                    // Removed all address-related fields from update
                ]);

            return redirect()->route('employee.index')->with('success', 'Empleado actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar empleado: ' . $e->getMessage())
                ->withInput()
                ->with('error_modal', 'edit')
                ->with('edit_id', $id);
        }
    }

    /**
     * Eliminar un empleado
     */
    public function destroy($id)
    {
        try {
            // Verificar si hay registros relacionados en activity_log
            // This check remains relevant if 'activity_log' still links to 'employee_id'
            $hasActivities = DB::table('activity_log')
                ->where('employee_id', $id)
                ->exists();

            if ($hasActivities) {
                return redirect()->back()->with('error', 'No se puede eliminar el empleado porque tiene actividades registradas');
            }

            DB::table('employees')
                ->where('employee_id', $id)
                ->delete();

           return redirect()->route('employee.index')->with('success', 'Empleado eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar empleado: ' . $e->getMessage());
        }
    }
}