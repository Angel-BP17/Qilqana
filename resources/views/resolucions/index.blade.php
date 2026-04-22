@extends('layouts.app')

@section('title', 'Resoluciones')
@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <span class="material-symbols-outlined me-2">description</span>Módulo de Resoluciones
                </h3>
                <p class="text-white-50 mb-0">Registro y vinculación de resoluciones directorales</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('errores'))
            <div class="alert alert-warning mt-3">
                <h5>Errores durante la importación:</h5>
                <ul>
                    @foreach (session('errores') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @php
            $hasChargePeriod = !empty($chargePeriod);
        @endphp
        <div class="row g-3 mb-4">
            @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('resolucion ingresar'))
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small text-uppercase mb-2"><span class="material-symbols-outlined me-1">add</span>Nuevo
                                registro</div>
                            @if ($hasChargePeriod)
                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                    data-bs-target="#createResolutionModal">
                                    <span class="material-symbols-outlined">description</span> Registrar resolucion
                                </button>
                            @else
                                <button type="button" class="btn btn-success w-100" disabled
                                    title="Configura el periodo en el modulo de configuracion">
                                    <span class="material-symbols-outlined">description</span> Registrar resolucion
                                </button>
                                <div class="text-muted small mt-2 text-danger">Falta configurar periodo</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('resolucion ver indicadores'))
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-muted small text-uppercase"><span class="material-symbols-outlined me-1">history_edu</span>Ultimo RD</div>
                                    <div class="fs-3 fw-bold mb-1">{{ $ultimoRegistro }}</div>
                                    <div class="text-muted small">Registro mas reciente</div>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-2">RD</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small text-uppercase"><span class="material-symbols-outlined me-1">calendar_today</span>Periodo</div>
                            @if ($hasChargePeriod)
                                <div class="fs-3 fw-bold mb-1">{{ $chargePeriod }}</div>
                                <div class="text-muted small">Actual</div>
                            @else
                                <div class="text-muted mt-2 small">Falta configurar</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small text-uppercase"><span class="material-symbols-outlined me-1">fact_check</span>Total
                            </div>
                            @if ($hasChargePeriod)
                                <div class="fs-3 fw-bold mb-1">{{ $totalResolucionesPeriodo }}</div>
                                <div class="text-muted small">En {{ $chargePeriod }}</div>
                            @else
                                <div class="text-muted mt-2 small">Falta configurar</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small text-uppercase"><span class="material-symbols-outlined me-1">history_edu</span>Por firmar
                            </div>
                            @if ($hasChargePeriod)
                                <div class="fs-3 fw-bold mb-1">{{ $pendientesResolucionesPeriodo }}</div>
                                <div class="text-muted small">En {{ $chargePeriod }}</div>
                            @else
                                <div class="text-muted mt-2 small">Falta configurar</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if (Auth::user()->hasRole('ADMINISTRADOR') ||
                Auth::user()->can('resolucion exportar') ||
                Auth::user()->can('resolucion importar excel'))
            <div class="row g-3 mb-4">
                @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('resolucion exportar'))
                    <div class="col-12 col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body py-3">
                                <div class="row g-2 align-items-center">
                                    <div class="col-12">
                                        <div class="fw-semibold"><span class="material-symbols-outlined me-1">description</span>Reportes</div>
                                    </div>
                                </div>
                                <div class="row g-2 align-items-center mt-1">
                                    <div class="col-12 col-md-7">
                                        <div class="text-muted small">Exporta resoluciones en PDF o Excel</div>
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">

                                            <form method="GET" action="{{ route('resoluciones.pdf') }}" target="_blank">
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                                <input type="hidden" name="periodo" value="{{ request('periodo') }}">
                                                <button type="submit" class="btn btn-danger">
                                                    <span class="material-symbols-outlined">picture_as_pdf</span> PDF
                                                </button>
                                            </form>
                                            <a href="{{ route('resoluciones.excel') }}?search={{ request('search') }}&periodo={{ request('periodo') }}"
                                                class="btn btn-success d-flex align-items-center">
                                                <span class="material-symbols-outlined me-1">table_chart</span> Excel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('resolucion importar excel'))
                    <div class="col-12 col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body py-3 d-flex flex-column">
                                <div class="fw-semibold mb-3">
                                    <span class="material-symbols-outlined me-1">upload</span>Importar resoluciones
                                </div>
                                <form method="POST" action="{{ route('index.import') }}" enctype="multipart/form-data" class="mt-auto">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <input type="file" class="form-control" id="archivo_excel"
                                                data-import-input="1" name="archivo_excel" accept=".xlsx,.xls">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex gap-2 h-100">
                                                <a href="{{ route('download.template') }}"
                                                    class="btn btn-info flex-grow-1 d-flex align-items-center justify-content-center text-white">
                                                    <span class="material-symbols-outlined me-1">download</span>Plantilla
                                                </a>
                                                <button type="submit"
                                                    class="btn btn-success flex-grow-1 d-flex align-items-center justify-content-center"
                                                    id="importExcelButton" disabled>
                                                    <span class="material-symbols-outlined me-1">upload</span> Importar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white border-0 py-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-lg-3">
                                <div class="h-100 d-flex flex-column justify-content-center">
                                    <h5 class="mb-1 fw-bold"><span class="material-symbols-outlined me-2">folder_open</span>Resoluciones</h5>
                                    <small class="opacity-75">Gestione resoluciones, cargos y firmas</small>
                                    <div class="d-flex gap-2 mt-3 d-md-none">
                                        <button type="button" class="btn btn-outline-light w-100"
                                            data-bs-toggle="collapse" data-bs-target="#resolutionFilters"
                                            aria-expanded="false" aria-controls="resolutionFilters">
                                            <span class="material-symbols-outlined">tune</span> Filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-9">
                                <div class="collapse d-md-block h-100" id="resolutionFilters">
                                    <form method="GET" action="{{ route('resolucions.index') }}"
                                        id="resolutionFilterForm" class="h-100">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-12 col-md-6 col-lg-6">
                                                <label class="form-label mb-1">Buscar</label>
                                                <input type="text" name="search" class="form-control"
                                                    placeholder="Buscar..." value="{{ request('search') }}">
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label mb-1">Periodo</label>
                                                <select name="periodo" class="form-select">
                                                    <option value="">Todos los periodos</option>
                                                    @foreach ($periodos as $periodo)
                                                        @if ($periodo !== null)
                                                            <option value="{{ $periodo }}"
                                                                {{ request('periodo') == $periodo ? 'selected' : '' }}>
                                                                {{ $periodo }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <label class="form-label mb-1 d-none d-md-block">Acciones</label>
                                                <div
                                                    class="d-flex flex-wrap gap-2 justify-content-md-end justify-content-lg-start">
                                                    <button type="submit" class="btn btn-dark shadow-sm">
                                                        <span class="material-symbols-outlined">filter_alt</span> Filtrar
                                                    </button>
                                                    <a href="{{ route('resolucions.index') }}?{{ http_build_query(request()->query()) }}"
                                                        class="btn btn-light shadow-sm text-dark">
                                                        <span class="material-symbols-outlined">refresh</span> Limpiar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if (request()->filled('search') || request()->filled('periodo'))
                            <div class="px-3 py-3 border-bottom bg-light bg-opacity-50">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-4">
                                        <div class="input-group input-group-sm shadow-sm">
                                            <span class="input-group-text bg-white border-end-0">
                                                <span class="material-symbols-outlined text-muted">filter_alt</span>
                                            </span>
                                            <input type="text" id="subfilter-input" class="form-control border-start-0 ps-0" 
                                                placeholder="Sub-filtrar resultados actuales...">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8 mt-2 mt-md-0 d-flex align-items-center justify-content-between">
                                        <small class="text-muted" id="subfilter-info">
                                            <span class="material-symbols-outlined me-1">info</span>
                                            Buscando en los {{ $resoluciones->count() }} resultados de esta página.
                                        </small>
                                        <div id="subfilter-results-info" class="d-none">
                                            <span class="badge bg-info rounded-pill" id="subfilter-counter">0</span>
                                            <small class="text-info fw-semibold ms-1">coincidencias</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenedor para "Sin resultados locales" -->
                            <div id="local-no-results" class="text-center py-5 d-none">
                                <span class="material-symbols-outlined fs-1 text-muted mb-2">search_off</span>
                                <p class="text-muted">No hay coincidencias en esta página.</p>
                                <button type="button" class="btn btn-warning btn-sm shadow-sm" id="btn-search-global">
                                    <span class="material-symbols-outlined">public</span> Buscar en todo el sistema
                                </button>
                            </div>

                            <!-- Tabla de Resoluciones -->
                            <div class="d-md-none">
                                @php
                                    $canSignResolution =
                                        Auth::user()->hasRole('ADMINISTRADOR') ||
                                        Auth::user()->can('modulo cargos') ||
                                        Auth::user()->can('modulo resoluciones');
                                    $canCreateCharge =
                                        Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo cargos');
                                    $canDeleteResolution = Auth::user()->hasRole('ADMINISTRADOR');
                                @endphp
                                @forelse ($resoluciones as $resolucion)
                                    @php
                                        $charge = $resolucion->charge;
                                        $signatureContent = null;
                                        if (
                                            $charge?->signature?->signature_root &&
                                            \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                                $charge->signature->signature_root,
                                            )
                                        ) {
                                            $signatureContent = \Illuminate\Support\Facades\Storage::disk('local')->get(
                                                $charge->signature->signature_root,
                                            );
                                        }
                                        $signatureStatus = $charge?->signature?->signature_status;
                                    @endphp
                                    <div class="border rounded-3 p-3 mb-3 bg-white shadow-sm">
                                        <div class="d-flex justify-content-between gap-2">
                                            <div>
                                                <div class="fw-semibold">RD {{ $resolucion->rd }}</div>
                                                <div class="text-muted small">
                                                    {{ $resolucion->fecha ? \Carbon\Carbon::parse($resolucion->fecha)->format('d/m/Y') : '' }}
                                                </div>
                                            </div>
                                            <div>
                                                @if ($signatureStatus === 'firmado')
                                                    <span class="badge bg-primary rounded-pill">Firmado</span>
                                                @elseif ($signatureStatus === 'rechazado')
                                                    <span class="badge bg-danger rounded-pill">Rechazado</span>
                                                @elseif ($signatureStatus === 'pendiente')
                                                    <span class="badge bg-warning text-dark rounded-pill">Pendiente</span>
                                                @else
                                                    <span class="badge bg-secondary rounded-pill">Sin cargo</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="text-muted small">Nombres y apellidos</div>
                                            <div>{{ $resolucion->nombres_apellidos }}</div>
                                        </div>
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            <span class="badge bg-light text-dark">DNI:
                                                {{ $resolucion->dni ?? '-' }}</span>
                                            <span class="badge bg-light text-dark">Periodo:
                                                {{ $resolucion->periodo }}</span>
                                            <span class="badge bg-light text-dark">Procedencia:
                                                {{ $resolucion->procedencia }}</span>
                                        </div>
                                        <div class="mt-2">
                                            <div class="text-muted small">Asunto</div>
                                            <div>{{ Str::limit($resolucion->asunto, 80) }}</div>
                                        </div>
                                        <div class="mt-3 d-flex flex-wrap gap-2">
                                            @if (!$charge && $canCreateCharge)
                                                <form method="POST"
                                                    action="{{ route('resolucions.charge.create', $resolucion) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                                        <span class="material-symbols-outlined">add</span> Crear cargo
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button"
                                                class="btn btn-outline-success btn-sm btn-sign-resolution" title="Firmar"
                                                data-action="{{ $charge ? route('charges.sign.store', $charge) : '' }}"
                                                data-charge='@json($charge)'
                                                data-signature='@json($signatureContent ?? '')'
                                                data-signer="{{ $charge?->signature?->signer?->name ?? '' }}"
                                                data-show-external="1" @disabled(!$charge || $signatureStatus !== 'pendiente' || !$canSignResolution)>
                                                <span class="material-symbols-outlined">history_edu</span> Firmar
                                            </button>
                                            @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                <button type="button" class="btn btn-warning btn-sm btn-edit-resolution"
                                                    data-action="{{ route('resolucions.update', $resolucion) }}"
                                                    data-rd="{{ $resolucion->rd }}"
                                                    data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('Y-m-d') : '' }}"
                                                    data-dni="{{ $resolucion->dni ?? '' }}"
                                                    data-nombres="{{ $resolucion->nombres_apellidos }}"
                                                    data-procedencia="{{ $resolucion->procedencia ?? '' }}"
                                                    data-asunto="{{ $resolucion->asunto }}">
                                                    <span class="material-symbols-outlined">edit</span> Editar
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-resolution"
                                                    data-action="{{ route('resolucions.destroy', $resolucion) }}"
                                                    title="{{ $canDeleteResolution ? 'Eliminar' : 'Solo administradores' }}"
                                                    @disabled(!$canDeleteResolution)>
                                                    <span class="material-symbols-outlined">delete</span> Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">No se encontraron resoluciones</div>
                                @endforelse
                            </div>
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light table-dark">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">RD</th>
                                            <th scope="col">Fecha</th>
                                            <th scope="col">Nombres y Apellidos</th>
                                            <th scope="col">DNI</th>
                                            <th scope="col">Asunto</th>
                                            <th scope="col">Periodo</th>
                                            <th scope="col">Procedencia</th>
                                            <th scope="col">Firma</th>
                                            @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                <th scope="col">Acciones</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    @php
                                        $canSignResolution =
                                            Auth::user()->hasRole('ADMINISTRADOR') ||
                                            Auth::user()->can('modulo cargos') ||
                                            Auth::user()->can('modulo resoluciones');
                                        $canCreateCharge =
                                            Auth::user()->hasRole('ADMINISTRADOR') ||
                                            Auth::user()->can('modulo cargos');
                                        $canDeleteResolution = Auth::user()->hasRole('ADMINISTRADOR');
                                    @endphp
                                    <tbody>
                                        @forelse ($resoluciones as $key => $resolucion)
                                            <tr>
                                                <td> {{ ($resoluciones->currentPage() - 1) * $resoluciones->perPage() + $key + 1 }}
                                                </td>
                                                <td>{{ $resolucion->rd }}</td>
                                                <td>{{ $resolucion->fecha ? \Carbon\Carbon::parse($resolucion->fecha)->format('d/m/Y') : '' }}
                                                </td>
                                                <td>{{ $resolucion->nombres_apellidos }}</td>
                                                <td>{{ $resolucion->dni ?? null }}</td>
                                                <td>{{ Str::limit($resolucion->asunto, 50) }}</td>
                                                <td class="text-center">{{ $resolucion->periodo }}</td>
                                                <td class="text-center">{{ $resolucion->procedencia }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $charge = $resolucion->charge;
                                                        $signatureContent = null;
                                                        if (
                                                            $charge?->signature?->signature_root &&
                                                            \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                                                $charge->signature->signature_root,
                                                            )
                                                        ) {
                                                            $signatureContent = \Illuminate\Support\Facades\Storage::disk(
                                                                'local',
                                                            )->get($charge->signature->signature_root);
                                                        }
                                                        $signatureStatus = $charge?->signature?->signature_status;
                                                    @endphp
                                                    <div
                                                        class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                        @if ($signatureStatus === 'firmado')
                                                            <span class="badge bg-primary">Firmado</span>
                                                        @elseif ($signatureStatus === 'rechazado')
                                                            <span class="badge bg-danger">Rechazado</span>
                                                        @elseif ($signatureStatus === 'pendiente')
                                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                                        @else
                                                            <span class="badge bg-secondary">Sin cargo</span>
                                                        @endif
                                                        @if (!$charge && $canCreateCharge)
                                                            <form method="POST"
                                                                action="{{ route('resolucions.charge.create', $resolucion) }}">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-outline-primary btn-sm">
                                                                    <span class="material-symbols-outlined">add</span> Crear cargo
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-sm btn-sign-resolution"
                                                            title="Firmar"
                                                            data-action="{{ $charge ? route('charges.sign.store', $charge) : '' }}"
                                                            data-charge='@json($charge)'
                                                            data-signature='@json($signatureContent ?? '')'
                                                            data-signer="{{ $charge?->signature?->signer?->name ?? '' }}"
                                                            data-show-external="1" @disabled(!$charge || $signatureStatus !== 'pendiente' || !$canSignResolution)>
                                                            <span class="material-symbols-outlined">history_edu</span>
                                                        </button>
                                                    </div>
                                                </td>
                                                @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-warning btn-edit-resolution"
                                                                data-action="{{ route('resolucions.update', $resolucion) }}"
                                                                data-rd="{{ $resolucion->rd }}"
                                                                data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('Y-m-d') : '' }}"
                                                                data-dni="{{ $resolucion->dni ?? '' }}"
                                                                data-nombres="{{ $resolucion->nombres_apellidos }}"
                                                                data-procedencia="{{ $resolucion->procedencia ?? '' }}"
                                                                data-asunto="{{ $resolucion->asunto }}">
                                                                <span class="material-symbols-outlined">edit</span>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-danger btn-delete-resolution"
                                                                data-action="{{ route('resolucions.destroy', $resolucion) }}"
                                                                title="{{ $canDeleteResolution ? 'Eliminar' : 'Solo administradores' }}"
                                                                @disabled(!$canDeleteResolution)><span class="material-symbols-outlined">delete</span></button>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                @php
                                                    $colspan = Auth::user()->hasRole('VISUALIZADOR') ? 9 : 10;
                                                @endphp
                                                <td colspan="{{ $colspan }}" class="text-center">No se encontraron
                                                    resoluciones</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Paginacin -->
                            <div id="pagination-container" class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Mostrando {{ $resoluciones->firstItem() }} a {{ $resoluciones->lastItem() }} de
                                    {{ $resoluciones->total() }} resultados
                                </div>
                                <div>
                                    {{ $resoluciones->appends(request()->query())->onEachSide(1)->links('pagination.bootstrap-4-lg') }}
                                </div>
                            </div>
                        @else
                            <div class="text-center px-3 py-4">
                                <img src="{{ asset('img/mesa de trabajo.png') }}" class="img-fluid mx-auto d-block"
                                    style="max-width: min(70vw, 360px); width: 100%; height: auto;" alt="Logo Qilqana">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal: Crear resolucion -->
        <div class="modal fade" id="createResolutionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Ingresar resolución</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        @include('resolucions.forms.create')
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Editar resolucion -->
        <div class="modal fade" id="editResolutionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Editar resolución</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        @include('resolucions.forms.edit')
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteResolutionModal" tabindex="-1" aria-labelledby="deleteResolutionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteResolutionModalLabel">Eliminar resolución</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="deleteResolutionForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="delete_resolution_reason" class="form-label">Razón</label>
                                <textarea class="form-control" id="delete_resolution_reason" name="reason" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('charges.forms.sign')

        <!-- Modal de Detalles (igual que antes) -->
    @endsection
