<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Etapas de Producción</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

{{-- Asumiendo que 'layouts.menu' es tu layout principal y define @yield('content_menu') --}}
@extends('layouts.menu') 
@section('content_menu')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Etapas de Producción</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStageModal">
            <i class="bi bi-plus-lg"></i> Nueva Etapa
        </button>
    </div>

    {{-- Mensajes de éxito y error (sesión) --}}
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
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Objetivo</th>
                            <th>Vendible</th> {{-- Encabezado de la columna para 'it_is_sellable' --}}
                            <th>Actividades</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stages as $stage)
                            <tr>
                                <td>{{ $stage->production_stages_id }}</td>
                                <td>{{ $stage->name }}</td>
                                <td>{{ $stage->stage_type_name }}</td>
                                <td>{{ $stage->quantity_to_produce }}</td>
                                <td>
                                    {{-- Muestra "SI" o "NO" basado en el valor de 'it_is_sellable' --}}
                                    @if($stage->it_is_sellable) 
                                        <span class="badge bg-success">SI</span>
                                    @else
                                        <span class="badge bg-secondary">NO</span>
                                    @endif
                                </td>
                                <td>{{ $stage->activity_count }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        {{-- Botón para abrir el modal de edición --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-stage" 
                                                data-id="{{ $stage->production_stages_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        {{-- Formulario para la eliminación (método DELETE) --}}
                                        <form action="{{ route('stages.destroy', $stage->production_stages_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE') {{-- Simula una solicitud DELETE --}}
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('¿Está seguro de que desea eliminar esta etapa?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">No hay etapas de producción registradas</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createStageModal" tabindex="-1" aria-labelledby="createStageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStageModalLabel">Nueva Etapa de Producción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Formulario para la creación de una nueva etapa --}}
                <form action="{{ route('stages.store') }}" method="POST">
                    @csrf {{-- Token CSRF para protección contra ataques --}}
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="stage_types_id" class="form-label">Tipo de Etapa <span class="text-danger">*</span></label>
                            <select class="form-select @error('stage_types_id') is-invalid @enderror" id="stage_types_id" name="stage_types_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($stageTypes as $type)
                                    <option value="{{ $type->stage_types_id }}" {{ old('stage_types_id') == $type->stage_types_id ? 'selected' : '' }}>
                                        {{ $type->stage_type_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stage_types_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity_to_produce" class="form-label">Objetivo de Producción <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity_to_produce') is-invalid @enderror" id="quantity_to_produce" 
                                   name="quantity_to_produce" value="{{ old('quantity_to_produce') }}" min="1" required>
                            @error('quantity_to_produce')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check mb-3">
                            {{-- Checkbox para 'it_is_sellable' --}}
                            <input class="form-check-input" type="checkbox" id="it_is_sellable" name="it_is_sellable" 
                                   {{ old('it_is_sellable') ? 'checked' : '' }}>
                            <label class="form-check-label" for="it_is_sellable">
                                Vendible
                            </label>
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

    <div class="modal fade" id="editStageModal" tabindex="-1" aria-labelledby="editStageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStageModalLabel">Editar Etapa de Producción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Formulario para la edición de una etapa existente --}}
                <form id="editStageForm" method="POST">
                    @csrf 
                    @method('PUT') {{-- Simula una solicitud PUT para la actualización --}}
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name" name="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_stage_types_id" class="form-label">Tipo de Etapa <span class="text-danger">*</span></label>
                            <select class="form-select @error('stage_types_id') is-invalid @enderror" id="edit_stage_types_id" name="stage_types_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($stageTypes as $type)
                                    <option value="{{ $type->stage_types_id }}">
                                        {{ $type->stage_type_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stage_types_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_quantity_to_produce" class="form-label">Objetivo de Producción <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity_to_produce') is-invalid @enderror" id="edit_quantity_to_produce" 
                                   name="quantity_to_produce" min="1" required>
                            @error('quantity_to_produce')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check mb-3">
                            {{-- Checkbox para 'it_is_sellable' en el modal de edición --}}
                            <input class="form-check-input" type="checkbox" id="edit_it_is_sellable" name="it_is_sellable"> 
                            <label class="form-check-label" for="edit_it_is_sellable">
                                Vendible
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lógica para reabrir el modal de creación si hay errores de validación (usando flash data)
        @if(session('error_modal') == 'create')
            var createModal = new bootstrap.Modal(document.getElementById('createStageModal'));
            createModal.show();
        @endif
        
        // Lógica para reabrir el modal de edición si hay errores de validación (usando flash data)
        // Y precargar el ID si es necesario
        @if(session('error_modal') == 'edit' && session('edit_id'))
            // Volver a cargar los datos para que el modal se muestre correctamente con los errores
            fetch(`/stages/{{ session('edit_id') }}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_name').value = data.name;
                    document.getElementById('edit_stage_types_id').value = data.stage_types_id;
                    document.getElementById('edit_quantity_to_produce').value = data.quantity_to_produce;
                    document.getElementById('edit_it_is_sellable').checked = data.it_is_sellable;
                    
                    const editForm = document.getElementById('editStageForm');
                    editForm.action = `/stages/{{ session('edit_id') }}`; // Asegura que la acción del formulario sea correcta
                    
                    var editModal = new bootstrap.Modal(document.getElementById('editStageModal'));
                    editModal.show();
                })
                .catch(error => {
                    console.error('Error al recargar datos para modal de edición:', error);
                });
        @endif
        
        // Manejar el clic en el botón de "Editar" de cada fila de la tabla
        const editButtons = document.querySelectorAll('.edit-stage');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const stageId = this.getAttribute('data-id');
                const editForm = document.getElementById('editStageForm');
                editForm.action = `/stages/${stageId}`; // Configura la URL de acción del formulario de edición

                // Realiza una petición AJAX para obtener los datos de la etapa
                fetch(`/stages/${stageId}/edit`)
                    .then(response => {
                        // Verifica si la respuesta HTTP es exitosa (código 200)
                        if (!response.ok) {
                            // Si no es exitosa, lanza un error para ser capturado por .catch()
                            throw new Error('Error de red o el servidor respondió con un error: ' + response.statusText);
                        }
                        return response.json(); // Parsea la respuesta JSON
                    })
                    .then(data => {
                        // Rellena los campos del formulario de edición con los datos obtenidos
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_stage_types_id').value = data.stage_types_id;
                        document.getElementById('edit_quantity_to_produce').value = data.quantity_to_produce;
                        // Establece el estado del checkbox 'it_is_sellable'
                        document.getElementById('edit_it_is_sellable').checked = data.it_is_sellable;
                        
                        // Muestra el modal de edición
                        const editModal = new bootstrap.Modal(document.getElementById('editStageModal'));
                        editModal.show();
                    })
                    .catch(error => {
                        // Captura cualquier error durante la petición (red, JSON, etc.)
                        console.error('Error al cargar los datos de la etapa:', error);
                        alert('Error al cargar los datos de la etapa para editar.');
                    });
            });
        });
    });
</script>
@endsection
</body>
</html>