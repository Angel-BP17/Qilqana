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
    <div class="card-header bg-info border-0 py-3 px-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold text-white">
                <span class="material-symbols-outlined me-2">group</span>Personas naturales
            </h5>
            <form action="{{ route('natural-people.index') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
                <div class="input-group input-group-sm flex-grow-1">
                    <span class="input-group-text bg-white border-end-0 input-lookup-special">
                        <span class="material-symbols-outlined text-muted">search</span>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 input-lookup-special" 
                        placeholder="Buscar por DNI o nombres..." value="{{ request('search') }}" style="min-width: 300px;">
                </div>
                <button type="submit" class="btn btn-lookup-special btn-sm px-4">
                    <span class="material-symbols-outlined me-1">search</span> Buscar
                </button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- ... (vista móvil sin cambios) ... --}}

        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th><span class="material-symbols-outlined me-1">person</span> Persona</th>
                        <th style="width: 150px;"><span class="material-symbols-outlined me-1">badge</span> DNI</th>
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
                            <td><span class="badge bg-light text-dark border">{{ $naturalPerson->dni ?: 'N/A' }}</span></td>
                            <td class="text-muted small">{{ optional($naturalPerson->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-muted small">{{ optional($naturalPerson->updated_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        title="{{ $editTitle }}" data-action="{{ route('natural-people.update', $naturalPerson) }}"
                                        data-dni="{{ $naturalPerson->dni }}" data-nombres="{{ $naturalPerson->nombres }}"
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
