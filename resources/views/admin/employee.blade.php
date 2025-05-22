<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title> <meta name="csrf-token" content="{{ csrf_token() }}">

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

@extends('layouts.menu')
@section('content_menu')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Empleados</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
            <i class="bi bi-plus-lg"></i> Nuevo Empleado
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
                            <th>Nombre Completo</th> <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->employee_id }}</td>
                                <td>
                                    <div class="fw-bold">
                                        {{ $employee->name ?? '' }}
                                        {{ $employee->middle_name ?? '' }}
                                        {{ $employee->last_name_pather ?? '' }}
                                        {{ $employee->last_name_mother ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-employee"
                                                data-id="{{ $employee->employee_id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-employee"
                                                data-id="{{ $employee->employee_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('employee.destroy', $employee->employee_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('¿Está seguro de que desea eliminar este empleado?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4"> <div class="text-muted">No hay empleados registrados</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEmployeeModalLabel">Nuevo Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employee.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_name" class="form-label">Primer Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="create_name" name="name"
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_middle_name" class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control @error('middle_name') is-invalid @enderror" id="create_middle_name" name="middle_name"
                                       value="{{ old('middle_name') }}">
                                @error('middle_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_last_name_pather" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name_pather') is-invalid @enderror" id="create_last_name_pather" name="last_name_pather"
                                       value="{{ old('last_name_pather') }}" required>
                                @error('last_name_pather')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_last_name_mother" class="form-label">Apellido Materno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name_mother') is-invalid @enderror" id="create_last_name_mother" name="last_name_mother"
                                       value="{{ old('last_name_mother') }}" required>
                                @error('last_name_mother')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_fire_date" class="form-label">Fecha de Contratación <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('fire_date') is-invalid @enderror" id="create_fire_date" name="fire_date"
                                       value="{{ old('fire_date') }}" required>
                                @error('fire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Removed other fields like identification_number, phone, email, address, etc. --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Editar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editEmployeeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        @if(session('error_modal') == 'edit' && session('edit_id'))
                            @php
                                $employeeToEdit = DB::table('employees')
                                    ->where('employee_id', session('edit_id'))
                                    ->select('employee_id', 'name', 'middle_name', 'last_name_pather', 'last_name_mother', 'fire_date')
                                    ->first();
                            @endphp
                            @if($employeeToEdit)
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_name" class="form-label">Primer Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name" name="name"
                                               value="{{ old('name', $employeeToEdit->name ?? '') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_middle_name" class="form-label">Segundo Nombre</label>
                                        <input type="text" class="form-control @error('middle_name') is-invalid @enderror" id="edit_middle_name" name="middle_name"
                                               value="{{ old('middle_name', $employeeToEdit->middle_name ?? '') }}">
                                        @error('middle_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_last_name_pather" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name_pather') is-invalid @enderror" id="edit_last_name_pather" name="last_name_pather"
                                               value="{{ old('last_name_pather', $employeeToEdit->last_name_pather ?? '') }}" required>
                                        @error('last_name_pather')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_last_name_mother" class="form-label">Apellido Materno <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name_mother') is-invalid @enderror" id="edit_last_name_mother" name="last_name_mother"
                                               value="{{ old('last_name_mother', $employeeToEdit->last_name_mother ?? '') }}" required>
                                        @error('last_name_mother')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_fire_date" class="form-label">Fecha de Contratación <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('fire_date') is-invalid @enderror" id="edit_fire_date" name="fire_date"
                                               value="{{ old('fire_date', $employeeToEdit->fire_date ? \Carbon\Carbon::parse($employeeToEdit->fire_date)->format('Y-m-d') : '') }}" required>
                                        @error('fire_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @else
                                <p class="text-danger">No se pudo cargar el empleado para edición.</p>
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

    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewEmployeeModalLabel">Detalles del Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="employeeDetails">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Información del Empleado</h5>
                                <dl class="row">
                                    <dt class="col-sm-4">ID:</dt>
                                    <dd class="col-sm-8" id="detail_employee_id"></dd>

                                    <dt class="col-sm-4">Nombre Completo:</dt>
                                    <dd class="col-sm-8" id="detail_full_name"></dd>

                                    <dt class="col-sm-4">Fecha de Contratación:</dt>
                                    <dd class="col-sm-8" id="detail_fire_date"></dd>
                                </dl>
                            </div>
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
        // Show create modal if there are errors from a previous submission
        @if(session('error_modal') == 'create')
            var createModal = new bootstrap.Modal(document.getElementById('createEmployeeModal'));
            createModal.show();
        @endif

        // Show edit modal if there are errors from a previous submission
        // We no longer fetch data here, as Blade has already pre-populated the form if errors existed
        @if(session('error_modal') == 'edit' && session('edit_id'))
            var editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
            editModal.show();
        @endif

        // Handle click on edit button
        const editButtons = document.querySelectorAll('.edit-employee');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-id');
                const editForm = document.getElementById('editEmployeeForm');
                editForm.action = `/employee/${employeeId}`; // Set the form action dynamically

                // Fetch employee data
                fetch(`/employee/${employeeId}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        const modalBody = document.querySelector('#editEmployeeModal .modal-body');

                        // Populate the form fields with employee data
                        modalBody.innerHTML = `
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_name" class="form-label">Primer Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name"
                                           value="${data.name || ''}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_middle_name" class="form-label">Segundo Nombre</label>
                                    <input type="text" class="form-control" id="edit_middle_name" name="middle_name"
                                           value="${data.middle_name || ''}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_last_name_pather" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_last_name_pather" name="last_name_pather"
                                           value="${data.last_name_pather || ''}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_last_name_mother" class="form-label">Apellido Materno <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_last_name_mother" name="last_name_mother"
                                           value="${data.last_name_mother || ''}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_fire_date" class="form-label">Fecha de Contratación <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_fire_date" name="fire_date"
                                           value="${data.fire_date || ''}" required>
                                </div>
                            </div>
                        `;

                        // Show modal
                        const editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                        editModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cargar los datos del empleado para edición.');
                    });
            });
        });

        // Handle click on view details button
        const viewButtons = document.querySelectorAll('.view-employee');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-id');

                // Fetch employee data for viewing
                fetch(`/employee/${employeeId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Expecting JSON from show method now
                    })
                    .then(data => {
                        document.getElementById('detail_employee_id').textContent = data.employee_id;
                        document.getElementById('detail_full_name').textContent =
                            `${data.name || ''} ${data.middle_name || ''} ${data.last_name_pather || ''} ${data.last_name_mother || ''}`;
                        document.getElementById('detail_fire_date').textContent = data.fire_date ? new Date(data.fire_date).toLocaleDateString('es-ES') : 'N/A';

                        // Show modal
                        const viewModal = new bootstrap.Modal(document.getElementById('viewEmployeeModal'));
                        viewModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('employeeDetails').innerHTML = `
                            <div class="alert alert-danger">
                                Error al cargar los datos del empleado. Por favor, intente nuevamente.
                            </div>
                        `;
                        const viewModal = new bootstrap.Modal(document.getElementById('viewEmployeeModal'));
                        viewModal.show();
                    });
            });
        });
    });
</script>
@endsection
</body>
</html>