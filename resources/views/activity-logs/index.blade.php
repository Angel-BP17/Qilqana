@extends('layouts.app')

@section('title', 'Registro de actividades')
@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Módulo de Registro de Actividades
                </h3>
                <p class="text-white-50 mb-0">Auditoría detallada de cambios, accesos y acciones en el sistema</p>
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

        @php
            $modelLabels = [
                'User' => 'Usuario',
                'Charge' => 'Cargo',
                'Resolucion' => 'Resolución',
                'Interesado' => 'Interesado',
                'NaturalPerson' => 'Persona natural',
                'Entity' => 'Entidad',
                'LegalEntity' => 'Persona juridica',
                'Setting' => 'Configuración',
            ];
        @endphp

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info border-0 py-3 px-3 px-md-4">
                <div class="text-white">
                    <h5 class="mb-0 fw-bold">Registro de actividades</h5>
                    <small class="opacity-75">Auditoría de cambios y acciones del sistema</small>
                </div>
                <div class="mt-3">
                    <button class="btn btn-light btn-sm d-md-none w-100" type="button" data-bs-toggle="collapse"
                        data-bs-target="#activityFilters" aria-expanded="false" aria-controls="activityFilters">
                        <i class="fa-solid fa-sliders me-1"></i> Filtros
                    </button>
                    <div class="collapse d-md-block mt-3" id="activityFilters">
                        <form class="row g-3 align-items-end" method="GET" action="{{ route('activity-logs.index') }}">
                            <div class="col-12 col-md-9">
                                <div class="row g-3">
                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-white">Acción</label>
                                        <select class="form-select" name="action">
                                            <option value="">Todas</option>
                                            @foreach ($actions as $action)
                                                <option value="{{ $action }}" @selected(request('action') === $action)>
                                                    {{ ucfirst($action) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-white">Modelo</label>
                                        <select class="form-select" name="model">
                                            <option value="">Todos</option>
                                            @foreach ($models as $model)
                                                <option value="{{ $model }}" @selected(request('model') === $model)>
                                            {{ $modelLabels[$model] ?? $model }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-white">Usuario</label>
                                        <select class="form-select" name="user_id">
                                            <option value="">Todos</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                                                    {{ trim($user->name . ' ' . $user->last_name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-white">Buscar</label>
                                        <input type="text" class="form-control" name="search"
                                            value="{{ request('search') }}" placeholder="Acción, modelo o razón">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 d-flex flex-wrap gap-2 align-items-end justify-content-md-end">
                                <button class="btn btn-light h-100 d-flex align-items-center" type="submit">
                                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                                </button>
                                <a href="{{ route('activity-logs.index') }}"
                                    class="btn btn-outline-light h-100 d-flex align-items-center">
                                    <i class="fa-solid fa-rotate me-1"></i> Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Vista móvil: tarjetas -->
                <div class="d-md-none">
                    @forelse ($logs as $log)
                        @php
                            $action = strtolower($log->action ?? '');
                            $actionClass = match ($action) {
                                'create', 'created' => 'success',
                                'update', 'updated' => 'warning',
                                'delete', 'deleted' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold">
                                        {{ $log->user ? trim($log->user->name . ' ' . $log->user->last_name) : 'Sistema' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ optional($log->created_at)->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                                <span class="badge bg-{{ $actionClass }}">{{ ucfirst($action) }}</span>
                            </div>
                            <div class="mt-2">
                                <div class="text-muted small fw-bold">Modelo</div>
                                <div>{{ $modelLabels[$log->model] ?? $log->model }}</div>
                            </div>
                            <div class="mt-2">
                                <div class="text-muted small fw-bold">Razón</div>
                                <div>{{ $log->reason ?? 'Sin especificar' }}</div>
                            </div>
                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                @if ($log->before)
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-view-changes"
                                        data-title="Antes" data-changes='@json($log->before)'>
                                        Ver antes
                                    </button>
                                @endif
                                @if ($log->after)
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-view-changes"
                                        data-title="Después" data-changes='@json($log->after)'>
                                        Ver después
                                    </button>
                                @endif
                                @if (!$log->before && !$log->after)
                                    <span class="text-muted small">Sin cambios registrados</span>
                                @endif
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
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Modelo</th>
                                <th>Razón</th>
                                <th>Antes</th>
                                <th>Después</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                @php
                                    $action = strtolower($log->action ?? '');
                                    $actionClass = match ($action) {
                                        'create', 'created' => 'success',
                                        'update', 'updated' => 'warning',
                                        'delete', 'deleted' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->user ? trim($log->user->name . ' ' . $log->user->last_name) : 'Sistema' }}
                                    </td>
                                    <td><span class="badge bg-{{ $actionClass }}">{{ ucfirst($action) }}</span></td>
                                    <td>{{ $modelLabels[$log->model] ?? $log->model }}</td>
                                    <td>{{ $log->reason ?? 'Sin especificar' }}</td>
                                    <td>
                                        @if ($log->before)
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-view-changes"
                                                data-title="Antes" data-changes='@json($log->before)'>
                                                Ver
                                            </button>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->after)
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-view-changes"
                                                data-title="Después" data-changes='@json($log->after)'>
                                                Ver
                                            </button>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
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
            </div>
            @if ($logs->hasPages())
                <div class="card-footer bg-white">
                    {{ $logs->links('pagination.bootstrap-4-lg') }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="activityChangesModal" tabindex="-1" aria-labelledby="activityChangesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="activityChangesModalLabel">Cambios</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody id="activityChangesContent"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/activity-logs.js'])
@endsection
