@php
    $canEditInteresado = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('interesados.edit');
    $canDeleteInteresado = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('interesados.delete');
    $editTitle = $canEditInteresado ? 'Editar' : 'No tienes permiso para editar interesados';

    $getLabel = function ($interesado) {
        if ($interesado->tipo_interesado === 'Persona Juridica') {
            return $interesado->razon_social ?: $interesado->ruc ?: 'Sin razón social';
        }
        if ($interesado->tipo_interesado === 'Persona Natural') {
            $fullName = trim(($interesado->nombres ?? '') . ' ' . ($interesado->apellidos ?? ''));
            return $fullName !== '' ? $fullName : ($interesado->dni ?: 'Sin nombre');
        }
        return 'Trabajador UGEL';
    };
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3">
        <h5 class="mb-0 fw-bold text-white">Interesados</h5>
    </div>
    <div class="card-body p-0">
        <!-- Vista móvil: tarjetas -->
        <div class="d-md-none">
            @forelse ($interesados as $interesado)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-semibold">{{ $getLabel($interesado) }}</div>
                        </div>
                        <span class="badge bg-secondary">{{ $interesado->tipo_interesado }}</span>
                    </div>
                    <div class="mt-2 small text-muted">
                        {{ optional($interesado->created_at)->format('Y-m-d H:i') }}
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-edit-interesado"
                            title="{{ $editTitle }}" data-action="{{ route('interesados.update', $interesado) }}"
                            data-tipo="{{ $interesado->tipo_interesado }}" data-ruc="{{ $interesado->ruc }}"
                            data-razon="{{ $interesado->razon_social }}" data-dni="{{ $interesado->dni }}"
                            data-nombres="{{ $interesado->nombres }}"
                            data-apellido-paterno="{{ $interesado->apellido_paterno }}"
                            data-apellido-materno="{{ $interesado->apellido_materno }}"
                            data-cargo="{{ $interesado->cargo }}"
                            @disabled(!$canEditInteresado)>
                            <i class="fa-solid fa-pen"></i> Editar
                        </button>
                        @include('interesados.forms.delete', [
                            'interesado' => $interesado,
                            'disabled' => !$canDeleteInteresado,
                        ])
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-inbox me-1"></i> No hay registros.
                </div>
            @endforelse
        </div>

        <!-- Vista desktop: tabla -->
        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Interesado</th>
                        <th>Tipo</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($interesados as $key => $interesado)
                        <tr>
                            <td class="fw-semibold text-muted">
                                {{ ($interesados->currentPage() - 1) * $interesados->perPage() + $key + 1 }}
                            </td>
                            <td>{{ $getLabel($interesado) }}</td>
                            <td><span class="badge bg-secondary">{{ $interesado->tipo_interesado }}</span></td>
                            <td>{{ optional($interesado->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($interesado->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-interesado"
                                        title="{{ $editTitle }}" data-action="{{ route('interesados.update', $interesado) }}"
                                        data-tipo="{{ $interesado->tipo_interesado }}" data-ruc="{{ $interesado->ruc }}"
                                        data-razon="{{ $interesado->razon_social }}" data-dni="{{ $interesado->dni }}"
                                        data-nombres="{{ $interesado->nombres }}"
                                        data-apellido-paterno="{{ $interesado->apellido_paterno }}"
                                        data-apellido-materno="{{ $interesado->apellido_materno }}"
                                        data-cargo="{{ $interesado->cargo }}"
                                        @disabled(!$canEditInteresado)>
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    @include('interesados.forms.delete', [
                                        'interesado' => $interesado,
                                        'disabled' => !$canDeleteInteresado,
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox me-1"></i> No hay registros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($interesados->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $interesados->links('pagination.bootstrap-4-lg') }}
            </div>
        @endif
    </div>
</div>
