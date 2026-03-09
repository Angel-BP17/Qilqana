@php
    $canEditNaturalPerson = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('natural-people.edit');
    $canDeleteNaturalPerson = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('natural-people.delete');
    $editTitle = $canEditNaturalPerson ? 'Editar' : 'No tienes permiso para editar personas naturales';

    $getLabel = function ($naturalPerson) {
        $fullName = trim(($naturalPerson->nombres ?? '') . ' ' . ($naturalPerson->apellidos ?? ''));
        return $fullName !== '' ? $fullName : ($naturalPerson->dni ?: 'Sin nombre');
    };
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3">
        <h5 class="mb-0 fw-bold text-white">Personas naturales</h5>
    </div>
    <div class="card-body p-0">
        <div class="d-md-none">
            @forelse ($naturalPeople as $naturalPerson)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-semibold">{{ $getLabel($naturalPerson) }}</div>
                            <div class="text-muted small">DNI: {{ $naturalPerson->dni ?: 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        {{ optional($naturalPerson->created_at)->format('Y-m-d H:i') }}
                    </div>
                    <div class="mt-3 d-flex flex-column flex-sm-row gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-edit-natural-person"
                            title="{{ $editTitle }}" data-action="{{ route('natural-people.update', $naturalPerson) }}"
                            data-dni="{{ $naturalPerson->dni }}" data-nombres="{{ $naturalPerson->nombres }}"
                            data-apellido-paterno="{{ $naturalPerson->apellido_paterno }}"
                            data-apellido-materno="{{ $naturalPerson->apellido_materno }}"
                            @disabled(!$canEditNaturalPerson)>
                            <i class="fa-solid fa-pen"></i> Editar
                        </button>
                        @include('natural-people.forms.delete', [
                            'naturalPerson' => $naturalPerson,
                            'disabled' => !$canDeleteNaturalPerson,
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
                        <th>Persona</th>
                        <th>DNI</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($naturalPeople as $key => $naturalPerson)
                        <tr>
                            <td class="fw-semibold text-muted">
                                {{ ($naturalPeople->currentPage() - 1) * $naturalPeople->perPage() + $key + 1 }}
                            </td>
                            <td>{{ $getLabel($naturalPerson) }}</td>
                            <td>{{ $naturalPerson->dni ?: 'N/A' }}</td>
                            <td>{{ optional($naturalPerson->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($naturalPerson->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-natural-person"
                                        title="{{ $editTitle }}" data-action="{{ route('natural-people.update', $naturalPerson) }}"
                                        data-dni="{{ $naturalPerson->dni }}" data-nombres="{{ $naturalPerson->nombres }}"
                                        data-apellido-paterno="{{ $naturalPerson->apellido_paterno }}"
                                        data-apellido-materno="{{ $naturalPerson->apellido_materno }}"
                                        @disabled(!$canEditNaturalPerson)>
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    @include('natural-people.forms.delete', [
                                        'naturalPerson' => $naturalPerson,
                                        'disabled' => !$canDeleteNaturalPerson,
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
        @if ($naturalPeople->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $naturalPeople->links('pagination.bootstrap-4-lg') }}
            </div>
        @endif
    </div>
</div>
