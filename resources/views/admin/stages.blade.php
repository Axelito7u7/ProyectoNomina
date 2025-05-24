<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Etapas de Producción</title>
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

@extends('layouts.menu')
@section('content_menu')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Etapas de Producción</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStageModal">
            <i class="bi bi-plus-lg"></i> Nueva Etapa
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
                            <th>Nombre</th>
                            <th>Tipo de Etapa</th>
                            <th>Cantidad a Producir</th>
                            <th>Vendible</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stages as $stage)
                            <tr>
                                <td>{{ $stage->production_stages_id }}</td>
                                <td>{{ $stage->name }}</td>
                                <td>{{ $stage->stage_type_name }}</td>
                                <td>{{ number_format($stage->quantity_to_produce) }}</td>
                                <td>
                                    @if($stage->it_is_sellable)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-stage"
                                                data-id="{{ $stage->production_stages_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-stage"
                                                data-id="{{ $stage->production_stages_id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStageModalLabel">Nueva Etapa de Producción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('stages.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create_name" class="form-label">Nombre de la Etapa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="create_name" name="name"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="create_stage_types_id" class="form-label">Tipo de Etapa <span class="text-danger">*</span></label>
                            <select class="form-select @error('stage_types_id') is-invalid @enderror" id="create_stage_types_id" name="stage_types_id" required>
                                <option value="">Seleccione un tipo</option>
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
                            <label for="create_quantity_to_produce" class="form-label">Cantidad a Producir <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('quantity_to_produce') is-invalid @enderror" id="create_quantity_to_produce" name="quantity_to_produce"
                                   value="{{ old('quantity_to_produce') }}" min="0" required>
                            @error('quantity_to_produce')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input @error('it_is_sellable') is-invalid @enderror" type="checkbox" id="create_it_is_sellable" name="it_is_sellable"
                                   {{ old('it_is_sellable') ? 'checked' : '' }}>
                            <label class="form-check-label" for="create_it_is_sellable">
                                Es vendible
                            </label>
                            @error('it_is_sellable')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStageModalLabel">Editar Etapa de Producción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editStageForm" method="POST">
                    @csrf
                    @method('PUT') {{-- Use PUT method for update --}}
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nombre de la Etapa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name" name="name"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_stage_types_id" class="form-label">Tipo de Etapa <span class="text-danger">*</span></label>
                            <select class="form-select @error('stage_types_id') is-invalid @enderror" id="edit_stage_types_id" name="stage_types_id" required>
                                <option value="">Seleccione un tipo</option>
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
                            <label for="edit_quantity_to_produce" class="form-label">Cantidad a Producir <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('quantity_to_produce') is-invalid @enderror" id="edit_quantity_to_produce" name="quantity_to_produce"
                                   value="{{ old('quantity_to_produce') }}" min="0" required>
                            @error('quantity_to_produce')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check mb-3">
                            <input type="hidden" name="it_is_sellable" value="0">
                            <input class="form-check-input @error('it_is_sellable') is-invalid @enderror"
                                type="checkbox"
                                id="edit_it_is_sellable"
                                name="it_is_sellable"
                                value="1"
                                {{ old('it_is_sellable', $obj->it_is_sellable ?? false) ? 'checked' : '' }}>

                            <label class="form-check-label" for="edit_it_is_sellable">
                                Es vendible
                            </label>

                            @error('it_is_sellable')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

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

    <div class="modal fade" id="deleteStageModal" tabindex="-1" aria-labelledby="deleteStageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStageModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta etapa de producción? Esta acción es irreversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteStageForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to clear validation feedback
        function clearValidationFeedback(formElement) {
            formElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formElement.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        // Handle opening create modal (for clearing previous errors)
        const createStageModal = document.getElementById('createStageModal');
        createStageModal.addEventListener('show.bs.modal', function() {
            clearValidationFeedback(this);
            // Optionally reset form fields if needed
            this.querySelector('form').reset();
            // Reset checked state for it_is_sellable, as reset() might not handle it as expected with old()
            document.getElementById('create_it_is_sellable').checked = false;
        });

        // Show create modal if there are errors (from server-side validation)
        @if(session('error_modal') == 'create')
            const createModalInstance = new bootstrap.Modal(document.getElementById('createStageModal'));
            createModalInstance.show();
        @endif

        // Handle click on edit button
        const editButtons = document.querySelectorAll('.edit-stage');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const stageId = this.getAttribute('data-id');
                const editForm = document.getElementById('editStageForm');
                editForm.action = `/stages/${stageId}`; // Set the form action dynamically

                clearValidationFeedback(editForm); // Clear previous errors

                // Fetch data for the selected stage via AJAX
                fetch(`/stages/${stageId}/edit`) // This will now correctly hit ProductionController@show
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error de red o el servidor respondió con un error: ' + response.statusText);
                        }
                        return response.json(); // Parse the JSON response
                    })
                    .then(data => {
                        // Populate form fields with the obtained data
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_stage_types_id').value = data.stage_types_id;
                        document.getElementById('edit_quantity_to_produce').value = data.quantity_to_produce;
                        document.getElementById('edit_it_is_sellable').checked = data.it_is_sellable;

                        // Show the edit modal
                        const editModal = new bootstrap.Modal(document.getElementById('editStageModal'));
                        editModal.show();
                    })
                    .catch(error => {
                        console.error('Error al cargar los datos de la etapa:', error);
                        alert('Error al cargar los datos de la etapa para editar.');
                    });
            });
        });

        // Show edit modal if there are errors (from server-side validation)
        @if(session('error_modal') == 'edit' && session('edit_id'))
            const editModalInstance = new bootstrap.Modal(document.getElementById('editStageModal'));
            editModalInstance.show();
            // If there's an error, old() values will pre-populate the form.
            // We just need to set the action URL correctly for the edit form.
            const editForm = document.getElementById('editStageForm');
            editForm.action = `/stages/{{ session('edit_id') }}`;
        @endif

        // Handle click on delete button
        const deleteButtons = document.querySelectorAll('.delete-stage');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const stageId = this.getAttribute('data-id');
                const deleteForm = document.getElementById('deleteStageForm');
                deleteForm.action = `/stages/${stageId}`; // Set the form action dynamically

                const deleteModal = new bootstrap.Modal(document.getElementById('deleteStageModal'));
                deleteModal.show();
            });
        });
    });
</script>
@endsection
</body>
</html>