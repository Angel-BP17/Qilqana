@php
    $canEditUser = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('users.edit');
    $canDeleteUser = Auth::user()->hasRole('ADMINISTRADOR');
    $editTitle = $canEditUser ? 'Editar' : 'No tienes permiso para editar usuarios';
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-auto">
                <h5 class="mb-0 fw-bold text-white">Usuarios</h5>
            </div>
            <div class="col-12 col-md">
                @include('users.forms.filter')
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Vista móvil --}}
        <div class="d-md-none p-3">
            @foreach ($users as $user)
                @php $userRoles = $user->getRoleNames(); @endphp
                <div class="card border-0 shadow-sm mb-3 overflow-hidden border">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold text-dark">{{ $user->name }} {{ $user->last_name }}</div>
                                <div class="small text-muted">DNI: {{ $user->dni }}</div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                    <span class="material-symbols-outlined">more_vert</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center btn-user-info"
                                            data-name="{{ $user->name }}"
                                            data-last_name="{{ $user->last_name }}" data-dni="{{ $user->dni }}"
                                            data-user_type="{{ $userRoles->join(', ') }}"
                                            data-created_at="{{ optional($user->created_at)->format('Y-m-d H:i') }}"
                                            data-updated_at="{{ optional($user->updated_at)->format('Y-m-d H:i') }}">
                                            <span class="material-symbols-outlined me-2 fs-5">info</span> Detalles
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center btn-edit-user"
                                            title="{{ $editTitle }}" data-action="{{ route('users.update', $user) }}"
                                            data-name="{{ $user->name }}" 
                                            data-apellido_paterno="{{ $user->naturalPerson?->apellido_paterno ?? '' }}"
                                            data-apellido_materno="{{ $user->naturalPerson?->apellido_materno ?? '' }}"
                                            data-dni="{{ $user->dni }}"
                                            data-roles='@json($userRoles)' @disabled(!$canEditUser)>
                                            <span class="material-symbols-outlined me-2 fs-5 text-primary">edit</span> Editar
                                        </button>
                                    </li>
                                    @if($canDeleteUser)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        @include('users.forms.delete', [
                                            'user' => $user,
                                            'disabled' => !$canDeleteUser,
                                            'class' => 'dropdown-item d-flex align-items-center text-danger'
                                        ])
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="mb-0">
                            @forelse ($userRoles as $role)
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle me-1">{{ $role }}</span>
                            @empty
                                <span class="text-muted small">Sin rol</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>DNI</th>
                        <th>Roles</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $user)
                        @php $userRoles = $user->getRoleNames(); @endphp
                        <tr>
                            <td class="fw-semibold text-muted">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $key + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->last_name }}</td>
                            <td>{{ $user->dni }}</td>
                            <td>
                                @forelse ($userRoles as $role)
                                    <span class="badge bg-secondary me-1 mb-1">{{ $role }}</span>
                                @empty
                                    <span class="text-muted">Sin rol</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-info btn-sm btn-user-info"
                                        title="Mas informacion" data-name="{{ $user->name }}"
                                        data-last_name="{{ $user->last_name }}" data-dni="{{ $user->dni }}"
                                        data-user_type="{{ $userRoles->join(', ') }}"
                                        data-created_at="{{ optional($user->created_at)->format('Y-m-d H:i') }}"
                                        data-updated_at="{{ optional($user->updated_at)->format('Y-m-d H:i') }}">
                                        <span class="material-symbols-outlined">info</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-user"
                                        title="{{ $editTitle }}" data-action="{{ route('users.update', $user) }}"
                                        data-name="{{ $user->name }}" 
                                        data-apellido_paterno="{{ $user->naturalPerson?->apellido_paterno ?? '' }}"
                                        data-apellido_materno="{{ $user->naturalPerson?->apellido_materno ?? '' }}"
                                        data-dni="{{ $user->dni }}"
                                        data-roles='@json($userRoles)' @disabled(!$canEditUser)>
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    @include('users.forms.delete', [
                                        'user' => $user,
                                        'disabled' => !$canDeleteUser,
                                    ])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $users->links('pagination.bootstrap-4-lg') }}
            </div>
        @endif
    </div>
</div>
