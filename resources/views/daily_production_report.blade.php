<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción del Día</title>
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
    @section('content')

    <div class="container mt-4">
        <form id="produccionForm">
            <div class="card border-1">
                <div class="card-header bg-white text-center">
                    <h5 class="mb-0 fw-bold">PRODUCCIÓN DEL DÍA</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label">Fecha de procesamiento</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" name="fechaInicio">
                        </div>
                        <div class="col-sm-1 text-center">
                            <span>-</span>
                        </div>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" name="fechaFin">
                        </div>
                    </div>
                    
                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label">Fecha actual:</label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="date" class="form-control" name="fechaActual">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="setToday()">Hoy</a>
                                    <a class="dropdown-item" href="#" onclick="setYesterday()">Ayer</a>
                                    <a class="dropdown-item" href="#" onclick="setTomorrow()">Mañana</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-1 mt-3">
                <div class="card-body">
                    <div class="row fw-bold mb-2">
                        <div class="col-3">Empleado</div>
                        <div class="col-4">Actividad/Etapa</div>
                        <div class="col-2">Objetivo</div>
                        <div class="col-2">Prod.Final</div>
                        <div class="col-1"></div>
                    </div>

                    <div id="filas-container">
                        <!-- Fila 1 -->
                        <div class="row mb-2 align-items-center fila-produccion">
                            <div class="col-3">
                                    <button class="form-control dropdown-toggle text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Seleccionar empleado
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        @foreach ($Empleados as $Empleado)
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="seleccionarEmpleado(this)">
                                                    {{ $Empleado->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                            </div>
                            <div class="col-4">
                                <div class="dropdown">
                                    <button class="form-control dropdown-toggle text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Seleccionar actividad
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        @foreach ($Etapas_producion as $Etapa_produccion)
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="seleccionarActividad(this)">
                                                    {{ $Etapa_produccion->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <input type="hidden" name="actividad[]">
                                </div>
                            </div>
                            <div class="col-2">
                                <input type="number" class="form-control bg-light" name="objetivo[]">
                            </div>
                            <div class="col-2">
                                <input type="number" class="form-control" name="produccion[]">
                            </div>
                            <div class="col-1 text-center">
                                <button type="button" class="btn btn-danger btn-sm eliminar-fila" style="display: none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-primary" id="agregar-fila">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
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

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para establecer la fecha actual
        function setToday() {
            const today = new Date();
            document.querySelector('input[name="fechaActual"]').value = formatDate(today);
        }
        
        // Función para establecer la fecha de ayer
        function setYesterday() {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            document.querySelector('input[name="fechaActual"]').value = formatDate(yesterday);
        }
        
        // Función para establecer la fecha de mañana
        function setTomorrow() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.querySelector('input[name="fechaActual"]').value = formatDate(tomorrow);
        }
        
        // Función para formatear la fecha en formato YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        // Función para seleccionar una actividad
        function seleccionarActividad(elemento) {
            const texto = elemento.textContent;
            const boton = elemento.closest('.dropdown').querySelector('button');
            const input = elemento.closest('.dropdown').querySelector('input[type="hidden"]');
            boton.textContent = texto;
            input.value = texto;
        }
        // Funcion para seleccionar empleado
        function seleccionarEmpleado(elemento) {
            const texto = elemento.textContent;
            const boton = elemento.closest('.dropdown').querySelector('button');
            const input = elemento.closest('.dropdown').querySelector('input[type="hidden"]');
            boton.textContent = texto;
            input.value = texto;
        }
        
        // Establecer la fecha actual al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            setToday();
            
            // Agregar nueva fila
            document.getElementById('agregar-fila').addEventListener('click', function() {
                const filasContainer = document.getElementById('filas-container');
                const primeraFila = document.querySelector('.fila-produccion');
                const nuevaFila = primeraFila.cloneNode(true);
                
                // Limpiar los valores
                nuevaFila.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });
                
                nuevaFila.querySelector('button.dropdown-toggle').textContent = 'Seleccionar actividad';
                
                // Mostrar el botón de eliminar
                nuevaFila.querySelector('.eliminar-fila').style.display = 'block';
                
                filasContainer.appendChild(nuevaFila);
                
                // Agregar evento para eliminar fila
                nuevaFila.querySelector('.eliminar-fila').addEventListener('click', function() {
                    nuevaFila.remove();
                });
            });
            
            // Manejar el envío del formulario
            document.getElementById('produccionForm').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Formulario enviado correctamente');
                console.log('Datos del formulario:', new FormData(this));
            });
        });
    </script>
</body>
</html>