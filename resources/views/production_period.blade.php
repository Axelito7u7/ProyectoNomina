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
    @section('content_menu')
    <div class="container mt-6">
        
            <div class="card border-1">
                <div class="card-header bg-white text-center">
                    <h5 class="mb-0 fw-bold">PRODUCCIÓN DEL PERIODO</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 row align-items-center">
                        <label class="fw-bold col-sm-3 col-form-label">Periodo de procesamiento:</label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" name="fechaInicio"
                                value="{{ date('Y-m-d', strtotime($dates->start_date))}}" readonly>
                        </div>
                        <div class="col-sm-1 text-center">
                            <span>-</span>
                        </div>
                        <div class="col-sm-2">
                        <input type="date" class="form-control" name="fechaFin" required 
                            value="{{ date('Y-m-d', strtotime($dates->end_date)) }}" readonly>
                        </div>
                        <div class="col-sm-2">
                            <label class="fw-bold col-sm-12 col-form-label">Sueldo base:</label>
                        </div>
                        <div class="col-sm-1">
                            <label class="fw-bold col-sm-12 col-form-label"> ${{$dates-> wage_by_day }}</label>
                        </div>
                    </div>
                    
                    <div class="mb-3 row align-items-center">
                        <label class="fw-bold col-sm-3 col-form-label">Fecha actual:</label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" name="fechaInicio" required value="{{ old('fechaInicio', $date -> format('Y-m-d'))}}" readonly>
                        </div>
                        <div class="col-sm-1 text-center">
                            <span> </span>
                        </div>
                        
                        <div class="col-sm-2">
                            <form method="GET">
                                <select class="form-control" name="empleado" onchange="this.form.submit()">
                                    <option value="">Filtro empeleado</option>
                                    @foreach ($employees as $user)
                                    <option 
                                        value="{{$user->employee_id}}"
                                        @if(request('empleado') == $user -> employee_id) selected @endif>
                                        {{$user-> name }} 
                                        {{$user -> last_name_pather}} 
                                        {{$user -> last_name_mother}}
                                        
                                    </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <div class="col-sm-2">
                            <label class="fw-bold col-sm-16 col-form-label">Días del periodo:</label>
                        </div>
                        <div class="col-sm-1">
                            <label class="fw-bold col-sm-16 col-form-label">{{$i}}</label>
                        </div>
                    </div>


                    
                </div>
            </div>





            




            <div class="card border-1 mt-4">
                <div class="card-body">
                    
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">Fecha produccion</th>
                            <th scope="col">Empleado</th>
                            <th scope="col">Actividad/Etapa</th>
                            <th scope="col">Objetivo</th>
                            <th scope="col">Prod.Final</th>
                            <th scope="col">Prod/Ajust</th>
                            <th scope="col">Sueldo final</th>
                            </tr>
                        </thead>
                        <tbody>

                            


                            @foreach ($query as $query)

                            @php
                                $fechaProduccion = \Carbon\Carbon::parse($query->date_production)->toDateString();
                            @endphp

                            @if($fechaProduccion >= $startDate->toDateString() && $fechaProduccion <= $endDate->toDateString() )

                            @if(!request('empleado') || $query->employee_id == request('empleado'))

 

                            
                            <tr>
                            <td>{{$query -> date_production}}</td>
                            <td>{{$query -> userName}}
                            {{$query -> userPather}}
                            {{ $query -> userMother}}
                            </td>

                            <td>{{$query -> product_name}}</td>
                            <td>{{$query -> quantity_to_produced}}</td>
                            <td>{{$query -> quantity_produce}}</td>

                    <form id="produccionForm" action="{{ route ('save') }}"  method="POST">
                        @csrf
                            <td class="col-md-1 ">

                                <input hidden name="id_pp[]" value="{{$query -> activity_log_id}}">
                                <input class="size form-control form-control-sm" type="text" 
                                aria-label=".form-control-sm example" name="quantity_produced[]" 
                                value="{{$query -> quantity_produced}}"></td>


                            <td> {{$query->end_wage}}</td>
                            

                            @endif
                            @endif
                            @endforeach

                            </tr>
                        
                            
                        </tbody>
                        </table>

                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success">CIERRE</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        @if(session('success'))
            <div class="alert alert-success" role="alert">
            {{session('success')}}
            </div>
        @endif

    </div>

    @endsection

    

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>