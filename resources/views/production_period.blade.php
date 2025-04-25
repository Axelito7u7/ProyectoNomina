<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
    @extends('layouts.menu')
    @section('content')
    <div class="container mt-6">
        <form id="produccionForm">
            <div class="card border-1">
                <div class="card-header bg-white text-center">
                    <h5 class="mb-0 fw-bold">PRODUCCIÓN DEL PERIODO</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 row align-items-center">
                        <label class="fw-bold col-sm-3 col-form-label">Periodo de procesamiento:</label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" name="fechaInicio">
                        </div>
                        <div class="col-sm-1 text-center">
                            <span>-</span>
                        </div>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" name="fechaFin">
                        </div>
                        <div class="col-sm-2">
                            <label class="fw-bold col-sm-12 col-form-label">Sueldo base:</label>
                        </div>
                        <div class="col-sm-1">
                            <input class="form-control form-control-sm" type="text" aria-label=".form-control-sm example">
                        </div>
                    </div>
                    
                    <div class="mb-3 row align-items-center">
                        <label class="fw-bold col-sm-3 col-form-label">Fecha actual:</label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" name="fechaInicio">
                        </div>
                        <div class="col-sm-3 text-center">
                            <span> </span>
                        </div>
                        
                        <div class="col-sm-2">
                            <label class="fw-bold col-sm-16 col-form-label">Días del periodo:</label>
                        </div>
                        <div class="col-sm-1">
                            <input class="form-control form-control-sm" type="text" aria-label=".form-control-sm example">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-1 mt-4">
                <div class="card-body">
                    <div class="row fw-bold mb-3">
                        <div class="col-3">Empleado</div>
                        <div class="col-3">Actividad/Etapa</div>
                        <div class="col-2">Objetivo</div>
                        <div class="col-1">Prod/<br>Final</div>
                        <div class="col-1">Prod/<br>Ajust</div>
                        <div class="col-2">Sueldo final</div>
                    </div>

                    <div id="filas-container">
                        <!-- Fila 1 -->
                        <div class="row mb-2 align-items-center fila-produccion">
                            <div class="col-3">
                                    <button class="form-control dropdown-toggle text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Seleccionar empleado
                                    </button>
                                    </ul>
                            </div>
                            <div class="col-3">
                                <div class="dropdown">
                                    <button class="form-control dropdown-toggle text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Seleccionar actividad
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="seleccionarActividad(this)">
                                                </a>
                                            </li>
                                    </ul>

                                    <input type="hidden" name="actividad[]">
                                </div>
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control bg-light" name="objetivo[]">
                            </div>


                            <div class="col-1">
                                @foreach ($dbActivityLog as $dbActivityLog )
                                {{
                                    dbActivityLog -> id_empoye
                                }}
                                <input type="text" class="form-control" name="produccion[]">

                                @endforeach
                            </div>

                            
                            <div class="col-1">
                                <input type="text" class="form-control" name="produccion[]">
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control" name="produccion[]">
                            </div>
                            <div class="col-1 text-center">
                                <button type="button" class="btn btn-danger btn-sm" style="display: none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success">CIERRE</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    @endsection

    

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>