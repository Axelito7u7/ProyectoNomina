<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProductionController extends Controller
{
    /**
     * Mostrar lista de etapas de producción
     */
    public function index()
    {
        // Obtener tipos de etapas para el formulario de creación/edición
        $stageTypes = DB::table('stage_types')
            ->orderBy('stage_type_name')
            ->get();
            
        // Obtener todas las etapas de producción con su tipo de etapa
        $stages = DB::table('products_production_stages')
            // Aseguramos que la unión usa la columna correcta en ambas tablas
            ->join('stage_types', 'products_production_stages.stage_types_id', '=', 'stage_types.stage_types_id')
            ->select(
                'products_production_stages.*', // Seleccionar todas las columnas de la tabla de etapas
                'stage_types.stage_type_name'   // Seleccionar el nombre del tipo de etapa
            )
            ->orderBy('products_production_stages.name') // Ordenar por el nombre de la etapa
            ->get();
            
        // Para cada etapa, contar las actividades relacionadas (para la columna "Actividades")
        foreach ($stages as $stage) {
            $stage->activity_count = DB::table('activity_log')
                ->where('production_stages_id', $stage->production_stages_id)
                ->count();
        }

        return view('admin.stages', compact('stages', 'stageTypes'));
    }

    /**
     * Este método no es estrictamente necesario si el modal de creación
     * se carga en la vista 'index'. Se mantiene para coherencia
     * si se necesitara una vista 'create' dedicada.
     */
    public function create()
    {
        $stageTypes = DB::table('stage_types')
            ->orderBy('stage_type_name')
            ->get();
            
        // Idealmente, redirige a la vista principal con los datos necesarios para el modal
        return view('admin.stages', compact('stageTypes'));
    }

    /**
     * Almacenar una nueva etapa de producción en la base de datos.
     * @param Request $request La solicitud HTTP que contiene los datos del formulario.
     */
    public function store(Request $request)
    {
        // Reglas de validación para los datos de la nueva etapa
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // VALIDACIÓN CORREGIDA: 'exists:stage_types,stage_types_id' asegura que el ID exista
            // en la columna 'stage_types_id' de la tabla 'stage_types'.
            'stage_types_id' => 'required|exists:stage_types,stage_types_id',
            'quantity_to_produce' => 'required|integer|min:1',
            // Usamos 'it_is_sellable' que es el nombre correcto de la columna.
            'it_is_sellable' => 'boolean', 
        ]);

        // Si la validación falla, redirigir de vuelta con errores y abrir el modal de creación
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_modal', 'create'); // Flag para reabrir el modal en la vista
        }

        try {
            // Insertar los datos en la tabla 'products_production_stages'
            DB::table('products_production_stages')->insert([
                'name' => $request->name,
                'stage_types_id' => $request->stage_types_id,
                'quantity_to_produce' => $request->quantity_to_produce,
                // Almacenar el valor booleano de 'it_is_sellable' (true si el checkbox fue marcado, false si no)
                'it_is_sellable' => $request->has('it_is_sellable') ? true : false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Redirigir a la vista principal con un mensaje de éxito
            return redirect()->route('stages.index')->with('success', 'Etapa de producción creada exitosamente');
        } catch (\Exception $e) {
            // Capturar cualquier excepción de la base de datos y redirigir con un mensaje de error
            return redirect()->back()
                ->with('error', 'Error al crear etapa de producción: ' . $e->getMessage())
                ->withInput()
                ->with('error_modal', 'create');
        }
    }

    /**
     * Mostrar información detallada de una etapa de producción.
     * Este método es usado para la vista detallada de una etapa (si existiera)
     * o para cargar datos en un modal "show".
     * @param int $id El ID de la etapa a mostrar.
     */
    public function show($id)
    {
        $stage = DB::table('products_production_stages')
            // Aseguramos que la unión usa la columna correcta en ambas tablas
            ->join('stage_types', 'products_production_stages.stage_types_id', '=', 'stage_types.stage_types_id')
            ->select(
                'products_production_stages.*',
                'stage_types.stage_type_name'
            )
            ->where('products_production_stages.production_stages_id', $id)
            ->first();

        if (!$stage) {
            return redirect()->route('stages.index')->with('error', 'Etapa de producción no encontrada');
        }
        
        // Contar actividades para la etapa específica
        $stage->activity_count = DB::table('activity_log')
            ->where('production_stages_id', $id)
            ->count();
            
        // Obtener actividades recientes relacionadas con esta etapa
        $activities = DB::table('activity_log')
            ->join('employees', 'activity_log.employee_id', '=', 'employees.employee_id')
            ->join('biweekly', 'activity_log.biweekly_id', '=', 'biweekly.biweekly_id')
            ->select(
                'activity_log.*',
                'employees.name',
                'employees.last_name_pather',
                'employees.last_name_mother',
                'biweekly.day_for_biweekly',
                'biweekly.wage_by_day'
            )
            ->where('activity_log.production_stages_id', $id)
            ->orderBy('activity_log.date_production', 'desc')
            ->limit(20)
            ->get();
            
        // Calcular el salario para cada actividad (lógica existente)
        foreach ($activities as $activity) {
            if ($stage->quantity_to_produce > 0) {
                $activity->calculated_wage = ($activity->wage_by_day / $stage->quantity_to_produce) * $activity->quantity_produced;
            } else {
                $activity->calculated_wage = 0;
            }
        }
        
        // Obtener tipos de etapas (necesario si la vista 'show' incluye formularios o selectores)
        $stageTypes = DB::table('stage_types')
            ->orderBy('stage_type_name')
            ->get();

        return view('admin.stages', compact('stage', 'activities', 'stageTypes'));
    }

    /**
     * Obtener los datos de una etapa específica en formato JSON para rellenar
     * el formulario de edición en un modal.
     * @param int $id El ID de la etapa a editar.
     */
    public function edit($id)
    {
        $stage = DB::table('products_production_stages')
            ->where('production_stages_id', $id)
            ->first();
            
        // Si la etapa no se encuentra, devolver una respuesta JSON con error 404
        if (!$stage) {
            return response()->json(['error' => 'Etapa no encontrada'], 404);
        }
        
        // Devolver los datos de la etapa en formato JSON
        return response()->json($stage);
    }

    /**
     * Actualizar una etapa de producción existente en la base de datos.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados.
     * @param int $id El ID de la etapa a actualizar.
     */
    public function update(Request $request, $id)
    {
        // Reglas de validación para los datos actualizados
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // VALIDACIÓN CORREGIDA: 'exists:stage_types,stage_types_id'
            'stage_types_id' => 'required|exists:stage_types,stage_types_id',
            'quantity_to_produce' => 'required|integer|min:1',
            // Usamos 'it_is_sellable' consistentemente.
            'it_is_sellable' => 'boolean', 
        ]);

        // Si la validación falla, redirigir de vuelta con errores y reabrir el modal de edición
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_modal', 'edit') // Flag para reabrir el modal de edición
                ->with('edit_id', $id);      // Pasar el ID para mantener el contexto de edición
        }

        try {
            // Actualizar la etapa específica en la base de datos
            DB::table('products_production_stages')
                ->where('production_stages_id', $id)
                ->update([
                    'name' => $request->name,
                    'stage_types_id' => $request->stage_types_id,
                    'quantity_to_produce' => $request->quantity_to_produce,
                    // Actualizar el valor booleano de 'it_is_sellable'
                    'it_is_sellable' => $request->has('it_is_sellable') ? true : false,
                    'updated_at' => Carbon::now(), // Actualizar la marca de tiempo de modificación
                ]);

            // Redirigir a la vista principal con un mensaje de éxito
            return redirect()->route('stages.index')->with('success', 'Etapa de producción actualizada exitosamente');
        } catch (\Exception $e) {
            // Capturar cualquier excepción de la base de datos y redirigir con un mensaje de error
            return redirect()->back()
                ->with('error', 'Error al actualizar etapa de producción: ' . $e->getMessage())
                ->withInput()
                ->with('error_modal', 'edit')
                ->with('edit_id', $id);
        }
    }

    /**
     * Eliminar una etapa de producción de la base de datos.
     * @param int $id El ID de la etapa a eliminar.
     */
    public function destroy($id)
    {
        try {
            // Antes de eliminar, verificar si existen actividades relacionadas.
            // Si hay actividades, no se permite la eliminación para mantener la integridad referencial.
            $hasActivities = DB::table('activity_log')
                ->where('production_stages_id', $id)
                ->exists();

            if ($hasActivities) {
                return redirect()->back()->with('error', 'No se puede eliminar la etapa porque tiene actividades registradas.');
            }

            // Si no hay actividades relacionadas, proceder con la eliminación.
            DB::table('products_production_stages')
                ->where('production_stages_id', $id)
                ->delete();

            // Redirigir a la vista principal con un mensaje de éxito.
            return redirect()->route('stages.index')->with('success', 'Etapa de producción eliminada exitosamente.');
        } catch (\Exception $e) {
            // Capturar cualquier error inesperado y redirigir con un mensaje de error.
            return redirect()->back()->with('error', 'Error al eliminar etapa de producción: ' . $e->getMessage());
        }
    }
}