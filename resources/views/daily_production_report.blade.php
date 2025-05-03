<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción del Día</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card {
            border-radius: 0;
        }
        .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
@extends('layouts.menu')
@section('content_menu')

<div class="container mt-4">
    {{-- se pone en el metodo post para mandar los datos del form al controlador --}}
    <form id="produccionForm" action="/daily_production" method="POST">
        {{-- Laravel requie csrf para validar el form y funcione el post --}}
        @csrf
        <div class="card border-1">
            <div class="card-header bg-white text-center">
                <h5 class="mb-0 fw-bold">PRODUCCIÓN DEL DÍA</h5>
            </div>
            <div class="card-body">
                {{-- @foreach ($View_biweekly as $biweekly) --}}
                @if ($View_biweekly)
                @php $biweekly = $View_biweekly; @endphp

                <div class="mb-3 row align-items-center">
                    <label class="col-sm-3 col-form-label">Fecha de procesamiento</label>
                        {{-- se usa hidden para ocultar el input y se pueda mandar el id solamente sin que se vea en la pantalla --}}
                        <input type="hidden" name = id_fecha_procesamiento value="{{$biweekly->biweekly_id}}">
                    <div class="col-sm-3">
                        {{-- se puede usar disabled o readonly pero solo readonly permite mandar a la base --}}
                        <input type="date" value="{{ $biweekly->start_date }}"  readonly class="form-control">
                    </div>
                    <div class="col-sm-1 text-center">
                        <span>-</span>
                    </div>
                    <div class="col-sm-3">
                        <input type="date" value="{{ $biweekly->end_date }}" readonly class="form-control">
                    </div>
                </div>
                <div class="mb-3 row align-items-center">
                    <label class="fw-bold col-sm-3 col-form-label">Fecha actual:</label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="date" class="form-control" name="fechaActual" min="{{ $biweekly->start_date }}" max="{{ $biweekly->end_date }}" required>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-1 mt-3">
    <div class="card-body">
        <div class="row fw-bold mb-2">
            <div class="col-3">Empleado</div>
            <div class="col-4">Actividad/Etapa</div>
            <div class="col-2">Objetivo</div>
            <div class="col-2">Prod.Final</div>
        </div>

        <div id="filas-container">
            @foreach ($View_Employees as $View_Employee)
                <div class="row mb-2 align-items-center fila-produccion">
                    <div class="col-3">
                        <input type="hidden" name="empleado_id[]" value="{{ $View_Employee->employee_id }}" required>
                        <span>{{ $View_Employee->name }} {{ $View_Employee->last_name_pather }} {{ $View_Employee->last_name_mother }}</span>
                    </div>
                    <div class="col-4 fila-produccion">
                        <select class="form-control" name="actividad[]" required onchange="seleccionarActividad(this)">
                            <option value="">Seleccionar actividad</option>
                            @foreach ($View_product_production as $product_production)
                                <option 
                                    value="{{ $product_production->production_stages_id }}" 
                                    data-cantidad="{{ $product_production->quantity_to_produce }}">
                                    {{ $product_production->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control bg-light" name="objetivo[]" disabled>
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control" name="produccion[]" required>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>


        <div class="row mt-3">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </form>
</div>

@endsection

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
function seleccionarActividad(elemento) {
    const objetivo = elemento.options[elemento.selectedIndex].getAttribute('data-cantidad');
    console.log('Objetivo seleccionado:', objetivo);  // Verifica el valor de objetivo

    // Buscar la fila correcta
    const fila = elemento.closest('.fila-produccion');
    console.log('Fila encontrada:', fila);  // Verifica que la fila sea la correcta

    const inputObjetivo = fila.querySelector('input[name="objetivo[]"]');
    console.log('Input objetivo:', inputObjetivo);  // Verifica que el input objetivo exista

    if (inputObjetivo) {
        inputObjetivo.value = objetivo;  // Solo asignar el valor si se encuentra el input
        inputObjetivo.disabled = false;
    }
}
</script>
</body>
</html>
