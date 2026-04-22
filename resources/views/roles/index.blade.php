@extends('layouts.app')

@section('title', 'Roles y permisos')
@section('content')
    <div class="container" id="roles-page" data-permission-labels='@json($permissionLabels)'>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <span class="material-symbols-outlined me-2">shield</span>Módulo de Roles y Permisos
                </h3>
                <p class="text-white-50 mb-0">Configuración de perfiles de usuario y niveles de acceso</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <span class="material-symbols-outlined fs-4">shield</span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total de roles</p>
                            <h4 class="mb-0 fw-bold">{{ $roles->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Permisos disponibles</p>
                            <h6 class="mb-0 fw-bold">{{ $permissions->count() }}</h6>
                        </div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#createRoleModal">
                            <span class="material-symbols-outlined">add</span> Nuevo rol
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-white">Roles y permisos</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Rol</th>
                                <th>Permisos</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $index => $role)
                                @php
                                    $rolePerms = $role->permissions->pluck('name');
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $index + 1 }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @forelse ($rolePerms as $perm)
                                            <span class="badge bg-secondary me-1 mb-1">
                                                {{ $permissionLabels[$perm] ?? $perm }}
                                            </span>
                                        @empty
                                            <span class="text-muted">Sin permisos</span>
                                        @endforelse
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-info btn-sm btn-role-info"
                                                title="Ver detalles" data-name="{{ $role->name }}"
                                                data-permissions='@json($rolePerms)'>
                                                <span class="material-symbols-outlined">info</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-edit-role"
                                                title="Editar" data-action="{{ route('roles.update', $role) }}"
                                                data-name="{{ $role->name }}"
                                                data-permissions='@json($rolePerms)'>
                                                <span class="material-symbols-outlined">edit</span>
                                            </button>
                                            <form method="POST" action="{{ route('roles.destroy', $role) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este rol?')">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal crear rol --}}
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="createRoleModalLabel">Crear rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del rol</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <p class="fw-semibold mb-1">Permisos</p>
                            <div class="row row-cols-1 row-cols-lg-2 g-3">
                                @foreach ($permissionGroups as $group)
                                    <div class="col">
                                        <div class="border rounded p-2 h-100 permission-group">
                                            <div class="form-check">
                                                <input class="form-check-input permission-module" type="checkbox"
                                                    name="permissions[]" value="{{ $group['module'] }}"
                                                    id="create_perm_module_{{ $loop->index }}">
                                                <label class="form-check-label"
                                                    for="create_perm_module_{{ $loop->index }}">
                                                    {{ $permissionLabels[$group['module']] ?? $group['module'] }}
                                                </label>
                                            </div>
                                            @if (!empty($group['permissions']))
                                                <select class="form-select select2-permissions mt-2" name="permissions[]"
                                                    multiple data-placeholder="Permisos del módulo">
                                                    @foreach ($group['permissions'] as $permission)
                                                        <option value="{{ $permission }}">
                                                            {{ $permissionLabels[$permission] ?? $permission }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <small class="text-muted d-block mt-2">Sin permisos adicionales.</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal editar rol --}}
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editRoleModalLabel">Editar rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editRoleForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del rol</label>
                            <input type="text" name="name" id="edit_role_name" class="form-control" required>
                        </div>
                        <div class="alert alert-info d-none" id="edit_role_admin_notice">
                            El rol ADMINISTRADOR tiene acceso total. No se pueden editar permisos.
                        </div>
                        <div class="mb-2">
                            <p class="fw-semibold mb-1">Permisos</p>
                            <div class="row row-cols-1 row-cols-lg-2 g-3">
                                @foreach ($permissionGroups as $group)
                                    <div class="col">
                                        <div class="border rounded p-2 h-100 permission-group">
                                            <div class="form-check">
                                                <input class="form-check-input edit-perm-module" type="checkbox"
                                                    name="permissions[]" value="{{ $group['module'] }}"
                                                    id="edit_perm_module_{{ $loop->index }}">
                                                <label class="form-check-label"
                                                    for="edit_perm_module_{{ $loop->index }}">
                                                    {{ $permissionLabels[$group['module']] ?? $group['module'] }}
                                                </label>
                                            </div>
                                            @if (!empty($group['permissions']))
                                                <select class="form-select select2-permissions edit-perm-select mt-2"
                                                    name="permissions[]" multiple
                                                    data-placeholder="Permisos del módulo">
                                                    @foreach ($group['permissions'] as $permission)
                                                        <option value="{{ $permission }}">
                                                            {{ $permissionLabels[$permission] ?? $permission }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <small class="text-muted d-block mt-2">Sin permisos adicionales.</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal info rol --}}
    <div class="modal fade" id="infoRoleModal" tabindex="-1" aria-labelledby="infoRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="infoRoleModalLabel">Detalles del rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-1">Nombre</p>
                    <h5 id="info_role_name" class="fw-bold mb-3">-</h5>
                    <p class="text-muted mb-1">Permisos</p>
                    <div id="info_role_permissions" class="d-flex flex-wrap gap-1"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
