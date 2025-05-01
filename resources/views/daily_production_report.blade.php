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
                            <input type="date" class="form-control" name="fechaActual" min="{{ $biweekly->start_date }}" max="{{ $biweekly->end_date }}">
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
                        <input type="hidden" name="empleado_id[]" value="{{ $View_Employee->employee_id }}">
                        <span>{{ $View_Employee->name }} {{ $View_Employee->last_name_pather }} {{ $View_Employee->last_name_mother }}</span>
                    </div>
                    <div class="col-4">
                        <div class="dropdown">
                            <button class="form-control dropdown-toggle text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Seleccionar actividad
                            </button>
                            <ul class="dropdown-menu w-100">
                            @foreach ($View_product_production as $product_production)
                                    <li>
                                    <!-- pertence al mismo componete "<a>" data cantidad es la variable donde se va guardar el valor de lo que seleciones de la base -->
                                        <a class="dropdown-item" href="#" onclick="seleccionarActividad(this)" 
                                        data-cantidad="{{ $product_production->quantity_to_produce }}"
                                        data-id="{{$product_production->production_stages_id}}">                               
                                        {{ $product_production->name }}
                                        </a>
                                    </li>
                             @endforeach
                            </ul>
                            <input type="hidden" name="actividad[]">
                        </div>
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control bg-light" name="objetivo[]" disabled>
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control" name="produccion[]">
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

        // se agrega una varible constante donde va recibir el valor de data cantidad
        const objetivo = elemento.getAttribute('data-cantidad');
        const id = elemento.getAttribute('data-id');
        const texto = elemento.textContent;
        const boton = elemento.closest('.dropdown').querySelector('button');
        const input = elemento.closest('.dropdown').querySelector('input[type="hidden"]');
        boton.textContent = texto;
        input.value = id;

        //se crea otra constante donde se guarda el elemnto de la fila produccionForm y se mandara para que tenga ese valor
        const fila = elemento.closest('.fila-produccion');
        const inputObjetivo = fila.querySelector('input[name="objetivo[]"]');
        inputObjetivo.value = objetivo;
    }

    document.getElementById('produccionForm').addEventListener('submit', function (e) {
        e.preventDefault();
        alert('Formulario enviado correctamente');

        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
    });
</script>
</body>
</html>
