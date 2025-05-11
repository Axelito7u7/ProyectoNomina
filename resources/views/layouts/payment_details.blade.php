<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
<h1>Detalles del pago</h1>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Empleado</th>
            <th scope="col">Objetivo</th>
            <th scope="col">Prod.Total</th>
            <th scope="col">Sueldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($viewDatailsSalary as $activity_log)
            <tr>
                <td>{{$activity_log->date_production}}</td>
                <td>
                    {{ $activity_log->employee->name }}
                    {{ $activity_log->employee->last_name_pather }}
                    {{ $activity_log->employee->last_name_mother }}
                </td>
                <td>A</td>
                <td>A</td>
                <td>A</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>