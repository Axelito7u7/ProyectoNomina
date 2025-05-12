<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

    <style>
        :root {
            --color-bg: #ffffff;
            --color-text: #333333;
            --color-muted: #6c757d;
            --color-border: #eaeaea;
            --color-accent: #0ea5e9;
            --color-success: #10b981;
            --color-header: #f8fafc;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --font-main: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        
        body {
            background-color: #f9fafb;
            font-family: var(--font-main);
            color: var(--color-text);
            line-height: 1.5;
            padding: 1.5rem;
        }
        
        .container {
            max-width: 1140px;
        }
        
        .payment-card {
            background-color: var(--color-bg);
            border-radius: 0.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--color-text);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--color-border);
            text-align: center;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 500;
            background-color: var(--color-header);
            color: var(--color-text);
            border-bottom: 1px solid var(--color-border);
            padding: 0.75rem 1rem;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--color-border);
            vertical-align: middle;
        }
        
        .table tr:last-child td {
            border-bottom: none;
        }
        
        .table-borderless td, .table-borderless th {
            border: none;
        }
        
        .employee-section {
            margin-bottom: 1.5rem;
        }
        
        .employee-name {
            font-weight: 500;
            color: var(--color-accent);
            padding: 0.5rem 0;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--color-border);
        }
        
        .employee-total {
            font-weight: 500;
            text-align: right;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            border-top: 1px solid var(--color-border);
        }
        
        .employee-total-amount {
            font-weight: 600;
            color: var(--color-success);
        }
        
        .salary-amount {
            font-weight: 500;
            color: var(--color-success);
        }
        
        .summary-card {
            background-color: var(--color-bg);
            border-radius: 0.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border);
            padding: 1.5rem;
        }
        
        .summary-title {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 1rem;
            color: var(--color-text);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--color-border);
        }
        
        .summary-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .summary-label {
            color: var(--color-muted);
        }
        
        .summary-value {
            font-weight: 500;
        }
        
        .summary-value.total {
            font-weight: 600;
            color: var(--color-success);
        }
        
        @media (max-width: 768px) {
            .payment-card {
                padding: 1.25rem;
            }
            
            .table td, .table th {
                padding: 0.5rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <h1 class="page-title">Detalles del Pago</h1>
            
            @php
                $empleadoActual = null;
                $totalSueldoEmpleado = 0;
                $empleadosAgrupados = [];
                
                // Agrupar registros por empleado
                foreach ($viewDatailsSalary as $activity_log) {
                    $idEmpleado = $activity_log->employee_id;
                    if (!isset($empleadosAgrupados[$idEmpleado])) {
                        $empleadosAgrupados[$idEmpleado] = [
                            'nombre' => $activity_log->employee->name . ' ' . 
                                      $activity_log->employee->last_name_pather . ' ' . 
                                      $activity_log->employee->last_name_mother,
                            'registros' => [],
                            'totalSueldo' => 0
                        ];
                    }
                    
                    $objetivo = $activity_log->products_production_stage->quantity_to_produce;
                    $producido = $activity_log->quantity_produced;
                    $sueldoBase = $activity_log->biweekly->wage_by_day ?? 0;
                    
                    $sueldoDelDia = 0;
                    if ($objetivo > 0) {
                        $sueldoDelDia = ($producido / $objetivo) * $sueldoBase;
                    }
                    
                    $empleadosAgrupados[$idEmpleado]['registros'][] = [
                        'fecha' => $activity_log->date_production,
                        'actividad' => $activity_log->products_production_stage->name,
                        'objetivo' => $activity_log->products_production_stage->quantity_to_produce,
                        'produccion' => $activity_log->quantity_produced,
                        'sueldo' => $sueldoDelDia
                    ];
                    
                    $empleadosAgrupados[$idEmpleado]['totalSueldo'] += $sueldoDelDia;
                }
                
                $totalProduccion = 0;
                $totalSueldo = 0;
                
                foreach ($viewDatailsSalary as $activity_log) {
                    $totalProduccion += $activity_log->quantity_produced;
                    
                    $objetivo = $activity_log->products_production_stage->quantity_to_produce;
                    $producido = $activity_log->quantity_produced;
                    $sueldoBase = $activity_log->biweekly->wage_by_day ?? 0;
                    
                    if ($objetivo > 0) {
                        $totalSueldo += ($producido / $objetivo) * $sueldoBase;
                    }
                }
                
                $totalEmpleados = count($empleadosAgrupados);
            @endphp
            
            @foreach ($empleadosAgrupados as $idEmpleado => $empleado)
                <div class="employee-section">
                    <div class="employee-name">{{ $empleado['nombre'] }}</div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Actividad</th>
                                    <th>Objetivo</th>
                                    <th>Prod.Total</th>
                                    <th class="text-end">Sueldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($empleado['registros'] as $registro)
                                    <tr>
                                        <td>{{ $registro['fecha'] }}</td>
                                        <td>{{ $registro['actividad'] }}</td>
                                        <td>{{ $registro['objetivo'] }}</td>
                                        <td>{{ $registro['produccion'] }}</td>
                                        <td class="text-end salary-amount">${{ number_format($registro['sueldo'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="employee-total">
                        Total: <span class="employee-total-amount">${{ number_format($empleado['totalSueldo'], 2) }}</span>
                    </div>
                </div>
            @endforeach
            

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>