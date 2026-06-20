@php
    $canEditNaturalPerson = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('natural-people.edit');
    $canDeleteNaturalPerson = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('natural-people.delete');
    $editTitle = $canEditNaturalPerson ? 'Editar' : 'No tienes permiso para editar personas naturales';

    $getLabel = function ($naturalPerson) {
        $fullName = trim(($naturalPerson->nombres ?? '') . ' ' . ($naturalPerson->apellido_paterno ?? '') . ' ' . ($naturalPerson->apellido_materno ?? ''));
        return $fullName !== '' ? $fullName : ($naturalPerson->dni ?: ($naturalPerson->cedula ?: 'Sin nombre'));
    };
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3 px-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-auto">
                <h5 class="mb-0 fw-bold text-white">
                    <span class="material-symbols-outlined me-2">group</span>Personas naturales
                </h5>
            </div>
            <div class="col-12 col-md">
                <form action="{{ route('natural-people.index') }}" method="GET" class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <div class="flex-grow-1" style="max-width: 400px; min-width: 200px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <span class="material-symbols-outlined text-muted">search</span>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                placeholder="DNI, CÉDULA o nombres..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-lookup-special btn-sm px-4">
                        <span class="material-symbols-outlined me-1">search</span> Buscar
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Vista móvil --}}
        <div class="d-md-none p-3">
            @forelse ($naturalPeople as $naturalPerson)
                <div class="border rounded-3 p-3 mb-3 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold text-dark">{{ $getLabel($naturalPerson) }}</div>
                            <div class="text-muted small">
                                @if($naturalPerson->dni) DNI: {{ $naturalPerson->dni }} @endif
                                @if($naturalPerson->cedula) Cédula: {{ $naturalPerson->cedula }} @endif
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm btn-edit-natural-person"
                                title="{{ $editTitle }}" data-action="{{ route('natural-people.update', $naturalPerson) }}"
                                data-dni="{{ $naturalPerson->dni }}" 
                                data-cedula="{{ $naturalPerson->cedula }}"
                                data-nombres="{{ $naturalPerson->nombres }}"
                                data-apellido-paterno="{{ $naturalPerson->apellido_paterno }}"
                                data-apellido-materno="{{ $naturalPerson->apellido_materno }}"
                                @disabled(!$canEditNaturalPerson)>
                                <span class="material-symbols-outlined fs-6">edit</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">No hay registros.</div>
            @endforelse
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th><span class="material-symbols-outlined me-1">person</span> Persona</th>
                        <th style="width: 180px;"><span class="material-symbols-outlined me-1">badge</span> Identificación</th>
                        <th style="width: 180px;"><span class="material-symbols-outlined me-1">calendar_today</span> Registrado</th>
                        <th style="width: 180px;"><span class="material-symbols-outlined me-1">schedule</span> Actualizado</th>
                        <th class="text-end pe-4" style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($naturalPeople as $key => $naturalPerson)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">
                                {{ ($naturalPeople->currentPage() - 1) * $naturalPeople->perPage() + $key + 1 }}
                            </td>
                            <td class="fw-bold text-dark">{{ $getLabel($naturalPerson) }}</td>
                            <td>
                                @if($naturalPerson->dni)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">DNI: {{ $naturalPerson->dni }}</span>
                                @elseif($naturalPerson->cedula)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">CED: {{ $naturalPerson->cedula }}</span>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ optional($naturalPerson->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-muted small">{{ optional($naturalPerson->updated_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-natural-person"
                                        title="{{ $editTitle }}" data-action="{{ route('natural-people.update', $naturalPerson) }}"
                                        data-dni="{{ $naturalPerson->dni }}" 
                                        data-cedula="{{ $naturalPerson->cedula }}"
                                        data-nombres="{{ $naturalPerson->nombres }}"
                                        data-apellido-paterno="{{ $naturalPerson->apellido_paterno }}"
                                        data-apellido-materno="{{ $naturalPerson->apellido_materno }}"
                                        @disabled(!$canEditNaturalPerson)>
                                        <span class="material-symbols-outlined">edit</span>
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
                                <span class="material-symbols-outlined me-1">inbox</span> No hay registros.
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
