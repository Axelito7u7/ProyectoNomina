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
                    <div class="col-4">
                        <div class="dropdown">
                            <button class="form-control dropdown-toggle text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false" required>
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
    // Se obtiene el valor de data-cantidad del <a>, que representa el objetivo o cantidad a producir
    const objetivo = elemento.getAttribute('data-cantidad');

    // Se obtiene el valor de data-id del <a>, que es el ID real de la actividad (no el nombre)
    const id = elemento.getAttribute('data-id');

    // Se obtiene el texto visible (nombre) de la opción seleccionada
    const texto = elemento.textContent;

    // Busca el botón dentro del mismo dropdown (cambia "Seleccionar actividad" por el nombre)
    const boton = elemento.closest('.dropdown').querySelector('button');
    boton.textContent = texto;

    // Busca el input oculto donde se debe guardar el ID de la actividad seleccionada
    const input = elemento.closest('.dropdown').querySelector('input[name="actividad[]"]');
    input.value = id;

    // Busca la fila padre (con clase 'fila-produccion') para encontrar el input de objetivo
    const fila = elemento.closest('.fila-produccion');

    // Busca el input oculto correspondiente al objetivo (cantidad a producir)
    const inputObjetivo = fila.querySelector('input[name="objetivo[]"]');

    // Asigna la cantidad (objetivo) al input oculto
    inputObjetivo.value = objetivo;
}

// Evento para manejar el envío del formulario
document.getElementById('produccionForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Evita que el formulario se envíe de forma normal (refresco de página)

    alert('Formulario enviado correctamente'); // Muestra una alerta al usuario

    const formData = new FormData(this); // Crea un objeto con todos los datos del formulario

    // Muestra cada par clave-valor en la consola (útil para debug)
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
});
</script>
</body>
</html>
