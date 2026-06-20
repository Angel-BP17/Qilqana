@extends('layouts.app')

@section('title', 'Tipos de Asunto')
@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <span class="material-symbols-outlined me-2">subject</span>Tipos de Asunto
                </h3>
                <p class="text-white-50 mb-0">Gestión de asuntos de resoluciones y sus categorías</p>
            </div>
        </div>



        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <span class="material-symbols-outlined fs-4">subject</span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total de asuntos</p>
                            <h4 class="mb-0 fw-bold">{{ $asuntoTypes->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Nuevo asunto</p>
                            <h6 class="mb-0">Registra un nuevo tipo de asunto</h6>
                        </div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#createAsuntoModal">
                            <span class="material-symbols-outlined align-middle me-1">add</span> Registrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-info border-0 py-3 px-3 px-md-4 d-md-none">
                <h5 class="mb-0 fw-bold text-white">Tipos de asunto</h5>
            </div>
            <div class="card-body p-0">
                {{-- Vista móvil --}}
                <div class="d-md-none p-3">
                    @forelse ($asuntoTypes as $key => $asunto)
                        <div class="card border-0 shadow-sm mb-3 overflow-hidden border">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="fw-bold text-dark fs-5">{{ $asunto->name }}</div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-edit-asunto"
                                            data-action="{{ route('asunto-types.update', $asunto) }}"
                                            data-name="{{ $asunto->name }}"
                                            data-description="{{ $asunto->description }}"
                                            data-types="{{ $asunto->resolucionTypes->pluck('id')->toJson() }}">
                                            <span class="material-symbols-outlined fs-6">edit</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-asunto"
                                            data-action="{{ route('asunto-types.destroy', $asunto) }}">
                                            <span class="material-symbols-outlined fs-6">delete</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-muted small text-uppercase fw-bold mb-1">Resoluciones compatibles</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($asunto->resolucionTypes as $rt)
                                            <span class="badge bg-light text-dark border small">{{ $rt->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @if($asunto->description)
                                    <div class="mt-2">
                                        <p class="text-muted small mb-0">{{ $asunto->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <span class="material-symbols-outlined fs-1 d-block mb-2">subject</span>
                            No hay tipos de asunto registrados
                        </div>
                    @endforelse
                </div>

                <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead style="--bs-table-bg: #e2eafc; --bs-table-color: #002855;">
                        <tr>
                            <th class="ps-3 py-3" style="width: 50px;">#</th>
                            <th class="py-3">Nombre del Asunto</th>
                            <th class="py-3">Tipos de Resolución Compatibles</th>
                            <th class="text-end pe-3 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($asuntoTypes as $key => $asunto)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $key + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $asunto->name }}</div>
                                    @if($asunto->description)
                                        <div class="text-muted small text-truncate" style="max-width: 200px;" title="{{ $asunto->description }}">
                                            {{ $asunto->description }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($asunto->resolucionTypes as $rt)
                                            <span class="badge bg-light text-dark border">{{ $rt->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-edit-asunto"
                                            data-action="{{ route('asunto-types.update', $asunto) }}"
                                            data-name="{{ $asunto->name }}"
                                            data-description="{{ $asunto->description }}"
                                            data-types="{{ $asunto->resolucionTypes->pluck('id')->toJson() }}">
                                            <span class="material-symbols-outlined fs-5">edit</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-asunto"
                                            data-action="{{ route('asunto-types.destroy', $asunto) }}">
                                            <span class="material-symbols-outlined fs-5">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined fs-1 d-block mb-2">subject</span>
                                    No hay tipos de asunto registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modales --}}
    @include('asunto-types.forms.create')
    @include('asunto-types.forms.edit')
    @include('asunto-types.forms.delete')

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2 en los modales
        if ($.fn.select2) {
            $('.select2-resolucion-types').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#createAsuntoModal')
            });
            $('.select2-resolucion-types-edit').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#editAsuntoModal')
            });
        }

        // Modal de edición
        const editModal = new bootstrap.Modal(document.getElementById('editAsuntoModal'));
        const editForm = document.getElementById('editAsuntoForm');
        
        document.querySelectorAll('.btn-edit-asunto').forEach(btn => {
            btn.onclick = () => {
                editForm.action = btn.dataset.action;
                document.getElementById('edit_asunto_name').value = btn.dataset.name;
                document.getElementById('edit_asunto_description').value = btn.dataset.description;
                
                const types = JSON.parse(btn.dataset.types);
                $('#edit_resolucion_types').val(types).trigger('change');

                editModal.show();
            };
        });

        // Modal de eliminación
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAsuntoModal'));
        const deleteForm = document.getElementById('deleteAsuntoForm');
        
        document.querySelectorAll('.btn-delete-asunto').forEach(btn => {
            btn.onclick = () => {
                deleteForm.action = btn.dataset.action;
                deleteModal.show();
            };
        });
    });
</script>
@endsection
