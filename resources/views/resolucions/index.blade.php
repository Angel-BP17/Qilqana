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
                            <div class="text-muted small text-uppercase mb-2"><span
                                    class="material-symbols-outlined me-1">add</span>Nuevo
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
                                    <div class="text-muted small text-uppercase"><span
                                            class="material-symbols-outlined me-1">history_edu</span>Ultimo RD</div>
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
                            <div class="text-muted small text-uppercase"><span
                                    class="material-symbols-outlined me-1">calendar_today</span>Periodo</div>
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
                            <div class="text-muted small text-uppercase"><span
                                    class="material-symbols-outlined me-1">fact_check</span>Total
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
                            <div class="text-muted small text-uppercase"><span
                                    class="material-symbols-outlined me-1">history_edu</span>Por firmar
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
                                        <div class="fw-semibold"><span
                                                class="material-symbols-outlined me-1">description</span>Reportes</div>
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
                                <form method="POST" action="{{ route('index.import') }}" enctype="multipart/form-data"
                                    class="mt-auto">
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
                                    <h5 class="mb-1 fw-bold"><span
                                            class="material-symbols-outlined me-2">folder_open</span>Resoluciones</h5>
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
                                            <input type="text" id="subfilter-input"
                                                class="form-control border-start-0 ps-0"
                                                placeholder="Sub-filtrar resultados actuales...">
                                        </div>
                                    </div>
                                    <div
                                        class="col-12 col-md-8 mt-2 mt-md-0 d-flex align-items-center justify-content-between">
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
                            <div class="d-md-none p-3">
                                @php
                                    $canSignResolution = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo cargos') || Auth::user()->can('modulo resoluciones');
                                    $canCreateCharge = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo cargos');
                                    $canDeleteResolution = Auth::user()->hasRole('ADMINISTRADOR');
                                @endphp
                                @forelse ($resoluciones as $resolucion)
                                    @php
                                        $charge = $resolucion->charge;
                                        $signatureStatus = $charge?->signature?->signature_status;
                                        $signatureContent = null;
                                        if ($charge?->signature?->signature_root && \Illuminate\Support\Facades\Storage::disk('local')->exists($charge->signature->signature_root)) {
                                            $signatureContent = \Illuminate\Support\Facades\Storage::disk('local')->get($charge->signature->signature_root);
                                        }
                                    @endphp
                                    <div class="card border-0 shadow-sm mb-3 overflow-hidden">
                                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">RD {{ $resolucion->rd }}</span>
                                                    <div class="small text-muted d-flex align-items-center">
                                                        <span class="material-symbols-outlined fs-6 me-1">calendar_today</span>
                                                        {{ $resolucion->fecha ? \Carbon\Carbon::parse($resolucion->fecha)->format('d/m/Y') : '---' }}
                                                    </div>
                                                </div>
                                                @include('charges.partials.status-badge', ['status' => $signatureStatus])
                                            </div>
                                        </div>
                                        <div class="card-body py-3">
                                            <div class="mb-3">
                                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Interesado</label>
                                                <div class="fw-semibold text-dark text-truncate" title="{{ $resolucion->nombres_apellidos }}">
                                                    {{ $resolucion->nombres_apellidos }}
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="text-muted small text-uppercase fw-bold d-block">DNI</label>
                                                    <span class="text-dark">{{ $resolucion->dni ?? '---' }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <label class="text-muted small text-uppercase fw-bold d-block">Periodo</label>
                                                    <span class="badge bg-light text-dark border">{{ $resolucion->periodo }}</span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Asunto</label>
                                                <div class="small text-dark lh-sm">{{ Str::limit($resolucion->asunto, 100) }}</div>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                                <button type="button" class="btn btn-outline-info btn-sm btn-view-res-details d-flex align-items-center"
                                                    title="Ver detalles"
                                                    data-rd="{{ $resolucion->rd }}"
                                                    data-interesado="{{ $resolucion->nombres_apellidos }}"
                                                    data-dni="{{ $resolucion->dni }}"
                                                    data-asunto="{{ $resolucion->asunto }}"
                                                    data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('d/m/Y') : '' }}"
                                                    data-periodo="{{ $resolucion->periodo }}"
                                                    data-procedencia="{{ $resolucion->procedencia }}"
                                                    data-has-charge="{{ $charge ? '1' : '0' }}"
                                                    data-has-signature="{{ $charge?->has_signature ? '1' : '0' }}"
                                                    data-signature-url="{{ $charge ? route('charges.file.signature', $charge) : '' }}"
                                                    data-signer="{{ $charge?->signature?->signer?->name ?? '' }} {{ $charge?->signature?->signer?->last_name ?? '' }}"
                                                    data-titularidad="{{ $charge?->signature?->titularidad ? '1' : '0' }}"
                                                    data-parentesco="{{ $charge?->signature?->parentesco ?? '' }}"
                                                    data-titular-name="{{ $resolucion->nombres_apellidos }}"
                                                    data-has-evidence="{{ $charge?->has_evidence ? '1' : '0' }}"
                                                    data-evidence-url="{{ $charge ? route('charges.file.evidence', $charge) : '' }}"
                                                    data-has-carta-poder="{{ $charge?->has_carta_poder ? '1' : '0' }}"
                                                    data-carta-poder-url="{{ $charge ? route('charges.file.carta-poder', $charge) : '' }}">
                                                    <span class="material-symbols-outlined fs-6 me-1">visibility</span> Detalles
                                                </button>

                                                @if (!$charge && $canCreateCharge)
                                                    <form method="POST" action="{{ route('resolucions.charge.create', $resolucion) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                                            <span class="material-symbols-outlined fs-6 me-1">add</span> Cargo
                                                        </button>
                                                    </form>
                                                @endif

                                                <button type="button" class="btn btn-outline-success btn-sm btn-sign-resolution d-flex align-items-center"
                                                    title="Firmar"
                                                    data-action="{{ $charge ? route('charges.sign.store', $charge) : '' }}"
                                                    data-charge='@json($charge)'
                                                    data-signature='@json($signatureContent ?? '')'
                                                    data-signer="{{ $charge?->signature?->signer?->name ?? '' }}"
                                                    data-show-external="1" @disabled(!$charge || $signatureStatus !== 'pendiente' || !$canSignResolution)>
                                                    <span class="material-symbols-outlined fs-6 me-1">history_edu</span> Firmar
                                                </button>

                                                @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                    <div class="ms-auto btn-group">
                                                        <button type="button" class="btn btn-outline-warning btn-sm btn-edit-resolution"
                                                            data-action="{{ route('resolucions.update', $resolucion) }}"
                                                            data-rd="{{ $resolucion->rd }}"
                                                            data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('Y-m-d') : '' }}"
                                                            data-dni="{{ $resolucion->dni ?? '' }}"
                                                            data-nombres="{{ $resolucion->nombres_apellidos }}"
                                                            data-procedencia="{{ $resolucion->procedencia ?? '' }}"
                                                            data-asunto="{{ $resolucion->asunto }}">
                                                            <span class="material-symbols-outlined fs-6">edit</span>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-resolution"
                                                            data-action="{{ route('resolucions.destroy', $resolucion) }}"
                                                            title="{{ $canDeleteResolution ? 'Eliminar' : 'Solo administradores' }}"
                                                            @disabled(!$canDeleteResolution)>
                                                            <span class="material-symbols-outlined fs-6">delete</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-5 bg-white rounded-3 shadow-sm">
                                        <span class="material-symbols-outlined fs-1 d-block mb-2">search_off</span>
                                        No se encontraron resoluciones
                                    </div>
                                @endforelse
                            </div>
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="--bs-table-bg: #e2eafc; --bs-table-color: #002855;">
                                        <tr class="border-bottom">
                                            <th class="ps-3 small fw-bold py-3" style="width: 50px;">#</th>
                                            <th class="small fw-bold py-3">Resolución</th>
                                            <th class="small fw-bold py-3">Interesado</th>
                                            <th class="small fw-bold py-3">Asunto</th>
                                            <th class="text-center small fw-bold py-3">Estado</th>
                                            @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                <th class="text-end pe-3 small fw-bold py-3">Acciones</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($resoluciones as $key => $resolucion)
                                            @php
                                                $charge = $resolucion->charge;
                                                $signatureStatus = $charge?->signature?->signature_status;
                                                $signatureContent = null;
                                                if ($charge?->signature?->signature_root && \Illuminate\Support\Facades\Storage::disk('local')->exists($charge->signature->signature_root)) {
                                                    $signatureContent = \Illuminate\Support\Facades\Storage::disk('local')->get($charge->signature->signature_root);
                                                }
                                            @endphp
                                            <tr>
                                                <td class="ps-3 text-muted small">
                                                    {{ ($resoluciones->currentPage() - 1) * $resoluciones->perPage() + $key + 1 }}
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary">RD {{ $resolucion->rd }}</div>
                                                    <div class="small text-muted d-flex align-items-center">
                                                        <span class="material-symbols-outlined fs-6 me-1">calendar_today</span>
                                                        {{ $resolucion->fecha ? \Carbon\Carbon::parse($resolucion->fecha)->format('d/m/Y') : '---' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $resolucion->nombres_apellidos }}">
                                                        {{ $resolucion->nombres_apellidos }}
                                                    </div>
                                                    <div class="small text-muted">DNI: {{ $resolucion->dni ?? '---' }}</div>
                                                </td>
                                                <td>
                                                    <div class="text-muted small lh-sm text-truncate" style="max-width: 250px;" title="{{ $resolucion->asunto }}">
                                                        {{ $resolucion->asunto }}
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-light text-muted border small fw-normal">{{ $resolucion->procedencia }}</span>
                                                        <span class="badge bg-light text-muted border small fw-normal">{{ $resolucion->periodo }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @include('charges.partials.status-badge', ['status' => $signatureStatus])
                                                </td>
                                                @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                    <td class="text-end pe-3">
                                                        <div class="d-flex justify-content-end gap-1">
                                                            {{-- Acción: Ver Detalles --}}
                                                            <button type="button" class="btn btn-outline-info btn-sm btn-view-res-details"
                                                                title="Ver detalles"
                                                                data-rd="{{ $resolucion->rd }}"
                                                                data-interesado="{{ $resolucion->nombres_apellidos }}"
                                                                data-dni="{{ $resolucion->dni }}"
                                                                data-asunto="{{ $resolucion->asunto }}"
                                                                data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('d/m/Y') : '' }}"
                                                                data-periodo="{{ $resolucion->periodo }}"
                                                                data-procedencia="{{ $resolucion->procedencia }}"
                                                                data-has-charge="{{ $charge ? '1' : '0' }}"
                                                                data-has-signature="{{ $charge?->has_signature ? '1' : '0' }}"
                                                                data-signature-url="{{ $charge ? route('charges.file.signature', $charge) : '' }}"
                                                                data-signer="{{ $charge?->signature?->signer?->name ?? '' }} {{ $charge?->signature?->signer?->last_name ?? '' }}"
                                                                data-titularidad="{{ $charge?->signature?->titularidad ? '1' : '0' }}"
                                                                data-parentesco="{{ $charge?->signature?->parentesco ?? '' }}"
                                                                data-titular-name="{{ $resolucion->nombres_apellidos }}"
                                                                data-has-evidence="{{ $charge?->has_evidence ? '1' : '0' }}"
                                                                data-evidence-url="{{ $charge ? route('charges.file.evidence', $charge) : '' }}"
                                                                data-has-carta-poder="{{ $charge?->has_carta_poder ? '1' : '0' }}"
                                                                data-carta-poder-url="{{ $charge ? route('charges.file.carta-poder', $charge) : '' }}">
                                                                <span class="material-symbols-outlined fs-5">visibility</span>
                                                            </button>

                                                            @if (!$charge && $canCreateCharge)
                                                                <form method="POST" action="{{ route('resolucions.charge.create', $resolucion) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-outline-primary btn-sm" title="Crear cargo pendiente">
                                                                        <span class="material-symbols-outlined fs-5">add_notes</span>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            {{-- Acción: Firmar --}}
                                                            <button type="button" class="btn btn-outline-success btn-sm btn-sign-resolution"
                                                                title="Firmar cargo"
                                                                data-action="{{ $charge ? route('charges.sign.store', $charge) : '' }}"
                                                                data-charge='@json($charge)'
                                                                data-signature='@json($signatureContent ?? '')'
                                                                data-signer="{{ $charge?->signature?->signer?->name ?? '' }}"
                                                                data-show-external="1" @disabled(!$charge || $signatureStatus !== 'pendiente' || !$canSignResolution)>
                                                                <span class="material-symbols-outlined fs-5">history_edu</span>
                                                            </button>

                                                            <div class="btn-group ms-1">
                                                                <button type="button" class="btn btn-outline-warning btn-sm btn-edit-resolution"
                                                                    data-action="{{ route('resolucions.update', $resolucion) }}"
                                                                    data-rd="{{ $resolucion->rd }}"
                                                                    data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('Y-m-d') : '' }}"
                                                                    data-dni="{{ $resolucion->dni ?? '' }}"
                                                                    data-nombres="{{ $resolucion->nombres_apellidos }}"
                                                                    data-procedencia="{{ $resolucion->procedencia ?? '' }}"
                                                                    data-asunto="{{ $resolucion->asunto }}" title="Editar">
                                                                    <span class="material-symbols-outlined fs-5">edit</span>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete-resolution"
                                                                    data-action="{{ route('resolucions.destroy', $resolucion) }}"
                                                                    title="{{ $canDeleteResolution ? 'Eliminar' : 'Solo administradores' }}"
                                                                    @disabled(!$canDeleteResolution)>
                                                                    <span class="material-symbols-outlined fs-5">delete</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ Auth::user()->hasRole('VISUALIZADOR') ? 5 : 6 }}" class="text-center py-5 text-muted">
                                                    <span class="material-symbols-outlined fs-1 d-block mb-2">search_off</span>
                                                    No se encontraron resoluciones
                                                </td>
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
        @include('resolucions.forms.view-details')
    @endsection
