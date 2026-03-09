@php
    $canEditEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('entities.edit');
    $canDeleteEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('entities.delete');
    $editTitle = $canEditEntity ? 'Editar' : 'No tienes permiso para editar entidades';
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3">
        <h5 class="mb-0 fw-bold text-white">Entidades</h5>
    </div>
    <div class="card-body p-0">
        <!-- Vista móvil: tarjetas -->
        <div class="d-md-none">
            @forelse ($entities as $entity)
                @php
                    $typeClass = $entity->school_type === 'Privada' ? 'primary' : 'secondary';
                @endphp
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-semibold">{{ $entity->name }}</div>
                            <div class="text-muted small">Código: {{ $entity->code }}</div>
                        </div>
                        <span class="badge bg-{{ $typeClass }}">{{ $entity->school_type }}</span>
                    </div>
                    <div class="mt-2">
                        <div class="text-muted small fw-bold">Distrito</div>
                        <div>{{ $entity->district }}</div>
                    </div>
                    <div class="mt-2">
                        <div class="text-muted small fw-bold">Contacto</div>
                        <div>{{ $entity->contact_number ?: 'Sin contacto' }}</div>
                    </div>
                    <div class="mt-2 d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-edit-entity"
                            title="{{ $editTitle }}" data-action="{{ route('entities.update', $entity) }}"
                            data-name="{{ $entity->name }}" data-code="{{ $entity->code }}"
                            data-district="{{ $entity->district }}"
                            data-contact="{{ $entity->contact_number }}"
                            data-type="{{ $entity->school_type }}"
                            @disabled(!$canEditEntity)>
                            <i class="fa-solid fa-pen"></i> Editar
                        </button>
                        @include('entities.forms.delete', [
                            'entity' => $entity,
                            'disabled' => !$canDeleteEntity,
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
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Distrito</th>
                        <th>Contacto</th>
                        <th>Tipo</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entities as $key => $entity)
                        @php
                            $typeClass = $entity->school_type === 'Privada' ? 'primary' : 'secondary';
                        @endphp
                        <tr>
                            <td class="fw-semibold text-muted">
                                {{ ($entities->currentPage() - 1) * $entities->perPage() + $key + 1 }}
                            </td>
                            <td>{{ $entity->name }}</td>
                            <td>{{ $entity->code }}</td>
                            <td>{{ $entity->district }}</td>
                            <td>{{ $entity->contact_number }}</td>
                            <td>
                                <span class="badge bg-{{ $typeClass }}">
                                    {{ $entity->school_type }}
                                </span>
                            </td>
                            <td>{{ optional($entity->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($entity->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-entity"
                                        title="{{ $editTitle }}" data-action="{{ route('entities.update', $entity) }}"
                                        data-name="{{ $entity->name }}" data-code="{{ $entity->code }}"
                                        data-district="{{ $entity->district }}"
                                        data-contact="{{ $entity->contact_number }}"
                                        data-type="{{ $entity->school_type }}"
                                        @disabled(!$canEditEntity)>
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    @include('entities.forms.delete', [
                                        'entity' => $entity,
                                        'disabled' => !$canDeleteEntity,
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox me-1"></i> No hay registros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($entities->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $entities->links('pagination.bootstrap-4-lg') }}
            </div>
        @endif
    </div>
</div>


