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
    <form>
        <div class="card border-1">
            <div class="card-header bg-white text-center">
                <h5 class="mb-0 fw-bold">PRODUCCIÓN DEL DÍA</h5>
            </div>
            <div class="card-body">
                {{-- @foreach ($last_biweely as $biweekly) --}}
                @if ($last_biweely)
                @php $biweekly = $last_biweely; @endphp

                <div class="mb-3 row align-items-center">
                    <label class="fw-bold col-sm-3 col-form-label">Periodo de procesamiento:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" value="{{ $biweekly->start_date }}"  readonly>
                    </div>
                    <div class="col-sm-1 text-center">
                        <span>-</span>
                    </div>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" value="{{ $biweekly->end_date }}" readonly>
                    </div>
                    <div class="col-sm-2">
                        <label class="fw-bold col-sm-12 col-form-label">Sueldo base:</label>
                    </div>
                    <div class="col-sm-1">
                        <input class="form-control form-control-sm" type="text" aria-label=".form-control-sm example" readonly value="{{$biweekly->wage_by_day}}">
                    </div>
                </div>
                
                <div class="mb-3 row align-items-center">
                    <label class="fw-bold col-sm-3 col-form-label">Fecha actual:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" name="fechaInicio" required value="<?php echo date("Y-m-d");?>" readonly>
                    </div>
                    <div class="col-sm-3 text-center">
                        <span> </span>
                    </div>
                    
                    <div class="col-sm-2">
                        <label class="fw-bold col-sm-16 col-form-label">Días del periodo:</label>
                    </div>
                    <div class="col-sm-1">
                        <input class="form-control form-control-sm" type="text" aria-label=".form-control-sm example" readonly value="{{$biweekly->day_for_biweekly}}">
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-1 mt-3">
    <div class="card-body">
        <div class="row fw-bold mb-2">
            <div class="col-3">Empleado</div>
            <div class="col-2">Dias trabajados</div>
            <div class="col-2">Ausencias</div>
            <div class="col-3">Dias de descanso</div>
            <div class="col-2">Paga total</div>
        </div>

        <div id="filas-container">
            @foreach ($View_final_salary as $View_Employee)
                <div class="row mb-2 align-items-center fila-produccion">
                    <div class="col-3">
                        <span>{{ $View_Employee->name }} {{ $View_Employee->last_name_father }} {{ $View_Employee->last_name_mother }}</span>
                    </div>
                    <div class="col-2 fila-produccion">
                        <span>{{ $View_Employee->days_worked }}</span>
                    </div>
                    <div class="col-2">
                        <span>{{ $View_Employee->absences }}</span>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control"  disabled>
                    </div>
                    <div class="col-2">
                        <span>{{ $View_Employee->total_salary }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>


        <div class="row mt-3">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Detalles</button>
            </div>
        </div>
    </form>
</div>

@endsection

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>

</script>
</body>
</html>
