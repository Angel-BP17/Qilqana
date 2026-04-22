@php
    $canEditLegalEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('legal-entities.edit');
    $canDeleteLegalEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('legal-entities.delete');
    $editTitle = $canEditLegalEntity ? 'Editar' : 'No tienes permiso para editar personas juridicas';
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3 px-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold text-white">
                <span class="material-symbols-outlined me-2">corporate_fare</span>Personas jurídicas
            </h5>
            <form action="{{ route('legal-entities.index') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
                <div class="input-group input-group-sm flex-grow-1">
                    <span class="input-group-text bg-white border-end-0 input-lookup-special">
                        <span class="material-symbols-outlined text-muted">search</span>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 input-lookup-special" 
                        placeholder="Buscar por RUC o razón social..." value="{{ request('search') }}" style="min-width: 300px;">
                </div>
                <button type="submit" class="btn btn-lookup-special btn-sm px-4">
                    <span class="material-symbols-outlined me-1">search</span> Buscar
                </button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="d-md-none">
            @forelse ($legalEntities as $legalEntity)
                <div class="border-bottom p-4">
                    <div class="fw-bold text-primary fs-5 mb-2">{{ $legalEntity->razon_social ?: 'Sin razón social' }}</div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-muted small text-uppercase fw-bold">RUC</div>
                            <div>{{ $legalEntity->ruc ?: 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small text-uppercase fw-bold">Distrito</div>
                            <div>{{ $legalEntity->district }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="text-muted small text-uppercase fw-bold">Representante</div>
                            <div class="text-dark">{{ $legalEntity->representative?->naturalPerson?->nombres ?: 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm px-3"
                            title="{{ $editTitle }}" data-action="{{ route('legal-entities.update', $legalEntity) }}"
                            data-ruc="{{ $legalEntity->ruc }}" data-razon="{{ $legalEntity->razon_social }}"
                            data-district="{{ $legalEntity->district }}" data-representative="{{ $legalEntity->representative_id }}" data-representative-dni="{{ $legalEntity->representative?->naturalPerson?->dni }}" data-representative-name="{{ $legalEntity->representative?->naturalPerson?->nombres }}" data-representative-cargo="{{ $legalEntity->representative?->cargo }}" data-representative-since="{{ $legalEntity->representative?->fecha_desde }}"
                            @disabled(!$canEditLegalEntity)>
                            <span class="material-symbols-outlined me-1">edit</span> Editar
                        </button>
                        @include('legal-entities.forms.delete', [
                            'legalEntity' => $legalEntity,
                            'disabled' => !$canDeleteLegalEntity,
                        ])
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <span class="material-symbols-outlined me-1">inbox</span> No hay registros.
                </div>
            @endforelse
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th><span class="material-symbols-outlined me-1">corporate_fare</span> Razón social</th>
                        <th style="width: 150px;"><span class="material-symbols-outlined me-1">badge</span> RUC</th>
                        <th style="width: 150px;"><span class="material-symbols-outlined me-1">distance</span> Distrito</th>
                        <th style="width: 180px;"><span class="material-symbols-outlined me-1">calendar_today</span> Registrado</th>
                        <th class="text-end pe-4" style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($legalEntities as $key => $legalEntity)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">
                                {{ ($legalEntities->currentPage() - 1) * $legalEntities->perPage() + $key + 1 }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $legalEntity->razon_social ?: 'Sin razon social' }}</div>
                                <div class="text-muted small">Rep: {{ $legalEntity->representative?->naturalPerson?->nombres ?: 'N/A' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $legalEntity->ruc ?: 'N/A' }}</span></td>
                            <td>{{ $legalEntity->district }}</td>
                            <td class="text-muted small">{{ optional($legalEntity->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        title="{{ $editTitle }}" data-action="{{ route('legal-entities.update', $legalEntity) }}"
                                        data-ruc="{{ $legalEntity->ruc }}" data-razon="{{ $legalEntity->razon_social }}"
                                        data-district="{{ $legalEntity->district }}" data-representative="{{ $legalEntity->representative_id }}" data-representative-dni="{{ $legalEntity->representative?->naturalPerson?->dni }}" data-representative-name="{{ $legalEntity->representative?->naturalPerson?->nombres }}" data-representative-cargo="{{ $legalEntity->representative?->cargo }}" data-representative-since="{{ $legalEntity->representative?->fecha_desde }}"
                                        @disabled(!$canEditLegalEntity)>
                                        <span class="material-symbols-outlined">edit</span>
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <span class="material-symbols-outlined me-1">inbox</span> No hay registros.
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
