@extends('layouts.app')

@section('title', 'Tipos de Resolución')
@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <span class="material-symbols-outlined me-2">category</span>Tipos de Resolución
                </h3>
                <p class="text-white-50 mb-0">Gestión de categorías para las resoluciones administrativas</p>
            </div>
        </div>



        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <span class="material-symbols-outlined fs-4">category</span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total de tipos</p>
                            <h4 class="mb-0 fw-bold">{{ $types->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Nuevo tipo</p>
                            <h6 class="mb-0">Registra una nueva categoría</h6>
                        </div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#createTypeModal">
                            <span class="material-symbols-outlined">add</span> Registrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-info border-0 py-3 px-3 px-md-4 d-md-none">
                <h5 class="mb-0 fw-bold text-white">Tipos de resolución</h5>
            </div>
            <div class="card-body p-0">
                {{-- Vista móvil --}}
                <div class="d-md-none p-3">
                    @forelse ($types as $key => $type)
                        <div class="card border-0 shadow-sm mb-3 overflow-hidden border">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">{{ $type->abreviacion ?? 'RD' }}</span>
                                        <div class="fw-bold text-dark fs-5">{{ $type->name }}</div>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-edit-type"
                                            data-action="{{ route('resolucion-types.update', $type) }}"
                                            data-name="{{ $type->name }}"
                                            data-abreviacion="{{ $type->abreviacion }}"
                                            data-description="{{ $type->description }}">
                                            <span class="material-symbols-outlined fs-6">edit</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-type"
                                            data-action="{{ route('resolucion-types.destroy', $type) }}">
                                            <span class="material-symbols-outlined fs-6">delete</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-muted small mb-0">{{ $type->description ?: 'Sin descripción' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <span class="material-symbols-outlined fs-1 d-block mb-2">category</span>
                            No hay tipos de resolución registrados
                        </div>
                    @endforelse
                </div>

                <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead style="--bs-table-bg: #e2eafc; --bs-table-color: #002855;">
                        <tr>
                            <th class="ps-3 py-3" style="width: 50px;">#</th>
                            <th class="py-3">Abrev.</th>
                            <th class="py-3">Nombre</th>
                            <th class="py-3">Descripción</th>
                            <th class="text-end pe-3 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($types as $key => $type)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $key + 1 }}</td>
                                <td class="fw-bold">{{ $type->abreviacion ?? '-' }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $type->name }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $type->description ?: '---' }}</div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-edit-type"
                                            data-action="{{ route('resolucion-types.update', $type) }}"
                                            data-name="{{ $type->name }}"
                                            data-abreviacion="{{ $type->abreviacion }}"
                                            data-description="{{ $type->description }}">
                                            <span class="material-symbols-outlined fs-5">edit</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-type"
                                            data-action="{{ route('resolucion-types.destroy', $type) }}">
                                            <span class="material-symbols-outlined fs-5">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined fs-1 d-block mb-2">category</span>
                                    No hay tipos de resolución registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modales --}}
    @include('resolucion-types.forms.create')
    @include('resolucion-types.forms.edit')
    @include('resolucion-types.forms.delete')

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal de edición
        const editModal = new bootstrap.Modal(document.getElementById('editTypeModal'));
        const editForm = document.getElementById('editTypeForm');
        
        document.querySelectorAll('.btn-edit-type').forEach(btn => {
            btn.onclick = () => {
                editForm.action = btn.dataset.action;
                document.getElementById('edit_name').value = btn.dataset.name;
                document.getElementById('edit_abreviacion').value = btn.dataset.abreviacion;
                document.getElementById('edit_description').value = btn.dataset.description;
                editModal.show();
            };
        });

        // Modal de eliminación
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteTypeModal'));
        const deleteForm = document.getElementById('deleteTypeForm');
        
        document.querySelectorAll('.btn-delete-type').forEach(btn => {
            btn.onclick = () => {
                deleteForm.action = btn.dataset.action;
                deleteModal.show();
            };
        });
    });
</script>
@endsection
