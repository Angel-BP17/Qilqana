@php
    $canEditLegalEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('legal-entities.edit');
    $canDeleteLegalEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('legal-entities.delete');
    $editTitle = $canEditLegalEntity ? 'Editar' : 'No tienes permiso para editar personas juridicas';
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold text-white">Personas juridicas</h5>
            <form action="{{ route('legal-entities.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                        placeholder="Buscar..." value="{{ request('search') }}" style="min-width: 200px;">
                </div>
                <button type="submit" class="btn btn-light btn-sm fw-bold text-info">Buscar</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="d-md-none">
            @forelse ($legalEntities as $legalEntity)
                <div class="border-bottom p-3">
                    <div class="fw-semibold">{{ $legalEntity->razon_social ?: 'Sin razon social' }}</div>
                    <div class="text-muted small">RUC: {{ $legalEntity->ruc ?: 'N/A' }}</div>
                    <div class="text-muted small">Distrito: {{ $legalEntity->district }}</div>
                    <div class="text-muted small">Representante: {{ $legalEntity->representative?->nombre ?: 'N/A' }}</div>
                    <div class="mt-3 d-flex flex-column flex-sm-row gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-edit-legal-entity"
                            title="{{ $editTitle }}" data-action="{{ route('legal-entities.update', $legalEntity) }}"
                            data-ruc="{{ $legalEntity->ruc }}" data-razon="{{ $legalEntity->razon_social }}"
                            data-district="{{ $legalEntity->district }}" data-representative="{{ $legalEntity->representative_id }}" data-representative-dni="{{ $legalEntity->representative?->dni }}" data-representative-name="{{ $legalEntity->representative?->nombre }}" data-representative-cargo="{{ $legalEntity->representative?->cargo }}" data-representative-since="{{ $legalEntity->representative?->fecha_desde }}"
                            @disabled(!$canEditLegalEntity)>
                            <i class="fa-solid fa-pen"></i> Editar
                        </button>
                        @include('legal-entities.forms.delete', [
                            'legalEntity' => $legalEntity,
                            'disabled' => !$canDeleteLegalEntity,
                        ])
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-inbox me-1"></i> No hay registros.
                </div>
            @endforelse
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Razon social</th>
                        <th>RUC</th>
                        <th>Distrito</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($legalEntities as $key => $legalEntity)
                        <tr>
                            <td class="fw-semibold text-muted">
                                {{ ($legalEntities->currentPage() - 1) * $legalEntities->perPage() + $key + 1 }}
                            </td>
                            <td>{{ $legalEntity->razon_social ?: 'Sin razon social' }}</td>
                            <td>{{ $legalEntity->ruc ?: 'N/A' }}</td>
                            <td>{{ $legalEntity->district }}</td>
                            <td>{{ optional($legalEntity->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($legalEntity->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-legal-entity"
                                        title="{{ $editTitle }}" data-action="{{ route('legal-entities.update', $legalEntity) }}"
                                        data-ruc="{{ $legalEntity->ruc }}" data-razon="{{ $legalEntity->razon_social }}"
                                        data-district="{{ $legalEntity->district }}" data-representative="{{ $legalEntity->representative_id }}" data-representative-dni="{{ $legalEntity->representative?->dni }}" data-representative-name="{{ $legalEntity->representative?->nombre }}" data-representative-cargo="{{ $legalEntity->representative?->cargo }}" data-representative-since="{{ $legalEntity->representative?->fecha_desde }}"
                                        @disabled(!$canEditLegalEntity)>
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    @include('legal-entities.forms.delete', [
                                        'legalEntity' => $legalEntity,
                                        'disabled' => !$canDeleteLegalEntity,
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox me-1"></i> No hay registros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($legalEntities->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $legalEntities->links('pagination.bootstrap-4-lg') }}
            </div>
        @endif
    </div>
</div>
