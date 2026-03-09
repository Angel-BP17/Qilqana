@php
    $canEditUser = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('users.edit');
    $canDeleteUser = Auth::user()->hasRole('ADMINISTRADOR');
    $editTitle = $canEditUser ? 'Editar' : 'No tienes permiso para editar usuarios';
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info border-0 py-3">
        <h5 class="mb-3 fw-bold text-white">Usuarios</h5>
        <div class="flex-wrap justify-content-between gap-2 align-items-center">
            @include('users.forms.filter')
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
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
                                        <i class="fa-solid fa-circle-info"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-user"
                                        title="{{ $editTitle }}" data-action="{{ route('users.update', $user) }}"
                                        data-name="{{ $user->name }}" data-last_name="{{ $user->last_name }}"
                                        data-dni="{{ $user->dni }}"
                                        data-roles='@json($userRoles)' @disabled(!$canEditUser)>
                                        <i class="fa-solid fa-pen"></i>
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
