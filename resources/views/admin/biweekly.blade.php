<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Períodos Quincenales</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

@extends('layouts.menu') {{-- Assuming this is your main layout --}}
@section('content_menu')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Períodos Quincenales</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBiweeklyModal">
            <i class="bi bi-plus-lg"></i> Nuevo Período
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Fecha Pago</th>
                            <th>Días</th>
                            <th>Salario por Día</th>
                            {{-- <th>Total Pagos</th> --}} {{-- Removed as requested --}}
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($biweeklies as $biweekly)
                            <tr>
                                <td>{{ $biweekly->biweekly_id }}</td>
                                <td>{{ \Carbon\Carbon::parse($biweekly->start_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($biweekly->end_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($biweekly->payment_date)->format('d/m/Y') }}</td>
                                <td>{{ $biweekly->days_count }}</td>
                                <td>${{ number_format($biweekly->wage_by_day, 2) }}</td>
                                <td>
                                    <span class="badge {{ $biweekly->status == 'Activo' ? 'bg-success' : 'bg-primary' }}">
                                        {{ $biweekly->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info view-biweekly"
                                                data-id="{{ $biweekly->biweekly_id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-biweekly"
                                                data-id="{{ $biweekly->biweekly_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Only show close button if period is Active (not closed) --}}
                                        @if($biweekly->status == 'Activo')
                                        <form action="{{ route('biweekly.close', $biweekly->biweekly_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('¿Está seguro de que desea cerrar este período quincenal? Esta acción marcará el período como cerrado y no se podrá modificar su estado.')">
                                                <i class="bi bi-check-circle"></i> Cerrar
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" class="btn btn-sm btn-success" disabled>
                                            <i class="bi bi-check-circle-fill"></i> Cerrado
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4"> {{-- Changed colspan from 8 to 7 --}}
                                    <div class="text-muted">No hay períodos quincenales registrados</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createBiweeklyModal" tabindex="-1" aria-labelledby="createBiweeklyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBiweeklyModalLabel">Nuevo Período Quincenal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('biweekly.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_start_date" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="create_start_date" name="start_date"
                                       value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_end_date" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="create_end_date" name="end_date"
                                       value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_payment_date" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="create_payment_date" name="payment_date"
                                       value="{{ old('payment_date') }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_day_for_biweekly" class="form-label">Día del Período (para cálculo) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('day_for_biweekly') is-invalid @enderror" id="create_day_for_biweekly" name="day_for_biweekly"
                                       value="{{ old('day_for_biweekly') }}" min="1" required>
                                @error('day_for_biweekly')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_wage_by_day" class="form-label">Salario Base por Día <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('wage_by_day') is-invalid @enderror" id="create_wage_by_day" name="wage_by_day"
                                       value="{{ old('wage_by_day') }}" min="0" required>
                                @error('wage_by_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBiweeklyModal" tabindex="-1" aria-labelledby="editBiweeklyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBiweeklyModalLabel">Editar Período Quincenal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editBiweeklyForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        {{-- Blade pre-population for errors on page load --}}
                        @if(session('error_modal') == 'edit' && session('edit_id'))
                            @php
                                $biweeklyToEdit = DB::table('biweekly')
                                    ->where('biweekly_id', session('edit_id'))
                                    ->first();
                            @endphp
                            @if($biweeklyToEdit)
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_start_date" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="edit_start_date" name="start_date"
                                               value="{{ old('start_date', $biweeklyToEdit->start_date ? \Carbon\Carbon::parse($biweeklyToEdit->start_date)->format('Y-m-d') : '') }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_end_date" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="edit_end_date" name="end_date"
                                               value="{{ old('end_date', $biweeklyToEdit->end_date ? \Carbon\Carbon::parse($biweeklyToEdit->end_date)->format('Y-m-d') : '') }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_payment_date" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="edit_payment_date" name="payment_date"
                                               value="{{ old('payment_date', $biweeklyToEdit->payment_date ? \Carbon\Carbon::parse($biweeklyToEdit->payment_date)->format('Y-m-d') : '') }}" required>
                                        @error('payment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_day_for_biweekly" class="form-label">Día del Período (para cálculo) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('day_for_biweekly') is-invalid @enderror" id="edit_day_for_biweekly" name="day_for_biweekly"
                                               value="{{ old('day_for_biweekly', $biweeklyToEdit->day_for_biweekly) }}" min="1" required>
                                        @error('day_for_biweekly')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_wage_by_day" class="form-label">Salario Base por Día <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('wage_by_day') is-invalid @enderror" id="edit_wage_by_day" name="wage_by_day"
                                               value="{{ old('wage_by_day', $biweeklyToEdit->wage_by_day) }}" min="0" required>
                                        @error('wage_by_day')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @else
                                <p class="text-danger">No se pudo cargar el período quincenal para edición.</p>
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewBiweeklyModal" tabindex="-1" aria-labelledby="viewBiweeklyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewBiweeklyModalLabel">Detalles del Período Quincenal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="biweeklyDetails">
                        {{-- Content will be loaded dynamically with JavaScript --}}
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Información del Período</h5>
                                <dl class="row">
                                    <dt class="col-sm-4">ID:</dt>
                                    <dd class="col-sm-8" id="detail_biweekly_id"></dd>

                                    <dt class="col-sm-4">Fecha Inicio:</dt>
                                    <dd class="col-sm-8" id="detail_start_date"></dd>

                                    <dt class="col-sm-4">Fecha Fin:</dt>
                                    <dd class="col-sm-8" id="detail_end_date"></dd>

                                    <dt class="col-sm-4">Fecha Pago:</dt>
                                    <dd class="col-sm-8" id="detail_payment_date"></dd>

                                    <dt class="col-sm-4">Días del Período:</dt>
                                    <dd class="col-sm-8" id="detail_days_count"></dd>

                                    <dt class="col-sm-4">Salario Base por Día:</dt>
                                    <dd class="col-sm-8" id="detail_wage_by_day"></dd>

                                    <dt class="col-sm-4">Total Pagos Generados:</dt>
                                    <dd class="col-sm-8" id="detail_total_wages"></dd> {{-- Display total wages --}}

                                    <dt class="col-sm-4">Estado:</dt>
                                    <dd class="col-sm-8" id="detail_status"></dd>
                                </dl>
                            </div>
                        </div>
                        <hr>
                        <h5 class="border-bottom pb-2">Pagos por Empleado</h5>
                        <div id="employee_wages_list">
                            {{-- Employee wages will be loaded here dynamically --}}
                            <p class="text-muted">Cargando detalles de pagos...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show create modal if there are errors
        @if(session('error_modal') == 'create')
            var createModal = new bootstrap.Modal(document.getElementById('createBiweeklyModal'));
            createModal.show();
        @endif

        // Show edit modal if there are errors
        @if(session('error_modal') == 'edit' && session('edit_id'))
            var editModal = new bootstrap.Modal(document.getElementById('editBiweeklyModal'));
            editModal.show();
        @endif

        // Handle click on edit button
        const editButtons = document.querySelectorAll('.edit-biweekly');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const biweeklyId = this.getAttribute('data-id');
                const editForm = document.getElementById('editBiweeklyForm');
                editForm.action = `/biweekly/${biweeklyId}`; // Set the form action dynamically

                fetch(`/biweekly/${biweeklyId}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        const modalBody = document.querySelector('#editBiweeklyModal .modal-body');
                        modalBody.innerHTML = `
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_start_date" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_start_date" name="start_date"
                                           value="${data.start_date || ''}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_end_date" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_end_date" name="end_date"
                                           value="${data.end_date || ''}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_payment_date" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_payment_date" name="payment_date"
                                           value="${data.payment_date || ''}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_day_for_biweekly" class="form-label">Día del Período (para cálculo) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_day_for_biweekly" name="day_for_biweekly"
                                           value="${data.day_for_biweekly || ''}" min="1" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_wage_by_day" class="form-label">Salario Base por Día <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="edit_wage_by_day" name="wage_by_day"
                                           value="${data.wage_by_day || ''}" min="0" required>
                                </div>
                            </div>
                        `;
                        const editModal = new bootstrap.Modal(document.getElementById('editBiweeklyModal'));
                        editModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cargar los datos del período quincenal para edición.');
                    });
            });
        });

        // Handle click on view details button
        const viewButtons = document.querySelectorAll('.view-biweekly');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const biweeklyId = this.getAttribute('data-id');
                const employeeWagesList = document.getElementById('employee_wages_list');
                employeeWagesList.innerHTML = '<p class="text-muted">Cargando detalles de pagos...</p>'; // Reset and show loading

                fetch(`/biweekly/${biweeklyId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(response_data => {
                        const biweekly_data = response_data.biweekly;
                        const employees_data = response_data.employees;

                        document.getElementById('detail_biweekly_id').textContent = biweekly_data.biweekly_id;
                        document.getElementById('detail_start_date').textContent = biweekly_data.start_date ? new Date(biweekly_data.start_date).toLocaleDateString('es-ES') : 'N/A';
                        document.getElementById('detail_end_date').textContent = biweekly_data.end_date ? new Date(biweekly_data.end_date).toLocaleDateString('es-ES') : 'N/A';
                        document.getElementById('detail_payment_date').textContent = biweekly_data.payment_date ? new Date(biweekly_data.payment_date).toLocaleDateString('es-ES') : 'N/A';
                        document.getElementById('detail_days_count').textContent = biweekly_data.days_count;
                        document.getElementById('detail_wage_by_day').textContent = `$${parseFloat(biweekly_data.wage_by_day).toFixed(2)}`;
                        document.getElementById('detail_total_wages').textContent = `$${parseFloat(biweekly_data.total_wages).toFixed(2)}`; // Display calculated total wages
                        document.getElementById('detail_status').textContent = biweekly_data.status;

                        // Populate employee wages list
                        let employeeListHtml = '';
                        if (employees_data.length > 0) {
                            employeeListHtml += `<table class="table table-sm table-striped"><thead><tr><th>Empleado</th><th>Cantidad Producida</th><th>Salario Calculado</th></tr></thead><tbody>`;
                            employees_data.forEach(employee => {
                                employeeListHtml += `
                                    <tr>
                                        <td>${employee.name || ''} ${employee.middle_name || ''} ${employee.last_name_pather || ''} ${employee.last_name_mother || ''}</td>
                                        <td>${employee.total_quantity_produced || 0}</td>
                                        <td>$${parseFloat(employee.total_wages_paid || 0).toFixed(2)}</td>
                                    </tr>
                                `;
                            });
                            employeeListHtml += `</tbody></table>`;
                        } else {
                            employeeListHtml = `<p class="text-muted">No hay empleados o actividades registradas para este período.</p>`;
                        }
                        employeeWagesList.innerHTML = employeeListHtml;

                        // Show modal
                        const viewModal = new bootstrap.Modal(document.getElementById('viewBiweeklyModal'));
                        viewModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('biweeklyDetails').innerHTML = `
                            <div class="alert alert-danger">
                                Error al cargar los datos del período quincenal. Por favor, intente nuevamente.
                            </div>
                        `;
                        const viewModal = new bootstrap.Modal(document.getElementById('viewBiweeklyModal'));
                        viewModal.show();
                    });
            });
        });
    });
</script>
@endsection
</body>
</html>