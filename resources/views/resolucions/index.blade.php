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



        @php
            $hasChargePeriod = !empty($chargePeriod);
            $canSignResolution = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo cargos') || Auth::user()->can('modulo resoluciones');
            $canCreateCharge = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo cargos');
            $canDeleteResolution = Auth::user()->hasRole('ADMINISTRADOR');
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
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-success py-2 px-1 w-100 shadow-sm d-flex align-items-center justify-content-center gap-1 fw-bold small text-truncate" 
                                            data-bs-toggle="modal" data-bs-target="#createResolutionModal" title="Registrar Resolucion">
                                            <span class="material-symbols-outlined fs-5">description</span> 
                                            <span class="small">Resolucion</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-primary py-2 px-1 w-100 shadow-sm d-flex align-items-center justify-content-center gap-1 fw-bold small text-truncate" 
                                            data-bs-toggle="modal" data-bs-target="#createChargeModal" title="Registrar cargo">
                                            <span class="material-symbols-outlined fs-5">note_add</span> 
                                            <span class="small">Registrar cargo</span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-success py-2 px-1 w-100 opacity-75 d-flex align-items-center justify-content-center gap-1 small text-truncate" disabled
                                            title="Configura el periodo en el modulo de configuracion">
                                            <span class="material-symbols-outlined fs-5">description</span> 
                                            <span class="small">Resolucion</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-primary py-2 px-1 w-100 opacity-75 d-flex align-items-center justify-content-center gap-1 small text-truncate" disabled
                                            title="Configura el periodo en el modulo de configuracion">
                                            <span class="material-symbols-outlined fs-5">note_add</span> 
                                            <span class="small">Registrar cargo</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="alert alert-warning py-2 px-3 mt-3 border-0 small shadow-sm d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined fs-6">warning</span>
                                    <span>Falta configurar el periodo actual</span>
                                </div>
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
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-2">RD</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small text-uppercase"><span
                                    class="material-symbols-outlined me-1">calendar_today</span>Periodo actual</div>
                            @if ($hasChargePeriod)
                                <div class="fs-3 fw-bold mb-1">{{ $chargePeriod }}</div>
                            @else
                                <div class="text-muted mt-2 small">Falta configurar</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small text-uppercase"><span
                                    class="material-symbols-outlined me-1">history_edu</span>Por firmar
                            </div>
                            @if ($hasChargePeriod)
                                <div class="fs-3 fw-bold mb-1">{{ $pendientesResolucionesPeriodo }}</div>
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
                                                <input type="hidden" name="search_rd" value="{{ request('search_rd') }}">
                                                <input type="hidden" name="search_asunto" value="{{ request('search_asunto') }}">
                                                <input type="hidden" name="periodo" value="{{ request('periodo') }}">
                                                <input type="hidden" name="resolucion_type_id" value="{{ request('resolucion_type_id') }}">
                                                <input type="hidden" name="asunto_type_id" value="{{ request('asunto_type_id') }}">
                                                <input type="hidden" name="level_modality_id" value="{{ request('level_modality_id') }}">
                                                <input type="hidden" name="desde" value="{{ request('desde') }}">
                                                <input type="hidden" name="hasta" value="{{ request('hasta') }}">
                                                <button type="submit" class="btn btn-danger" @disabled($resoluciones->isEmpty())>
                                                    <span class="material-symbols-outlined">picture_as_pdf</span> PDF
                                                </button>
                                            </form>
                                            <a href="{{ route('resoluciones.excel') }}?search_rd={{ request('search_rd') }}&search_asunto={{ request('search_asunto') }}&periodo={{ request('periodo') }}&resolucion_type_id={{ request('resolucion_type_id') }}&asunto_type_id={{ request('asunto_type_id') }}&level_modality_id={{ request('level_modality_id') }}&desde={{ request('desde') }}&hasta={{ request('hasta') }}"
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
                            <div class="col-12 col-lg-4">
                                <div class="h-100 d-flex flex-column justify-content-center">
                                    <h5 class="mb-1 fw-bold"><span
                                            class="material-symbols-outlined me-2">folder_open</span>Resoluciones</h5>
                                    <small class="opacity-75">Gestione resoluciones, cargos y firmas</small>
                                </div>
                            </div>
                            <div class="col-12 col-lg-8">
                                <div class="h-100">
                                    <form method="GET" action="{{ route('resolucions.index') }}"
                                        id="resolutionSearchForm" class="h-100">
                                        {{-- Conservar filtros activos al buscar texto --}}
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                        <input type="hidden" name="periodo" value="{{ request('periodo') }}">
                                        <input type="hidden" name="resolucion_type_id" value="{{ request('resolucion_type_id') }}">
                                        <input type="hidden" name="asunto_type_id" value="{{ request('asunto_type_id') }}">
                                        <input type="hidden" name="level_modality_id" value="{{ request('level_modality_id') }}">
                                        <input type="hidden" name="desde" value="{{ request('desde') }}">
                                        <input type="hidden" name="hasta" value="{{ request('hasta') }}">

                                        <div class="row g-2 align-items-end">
                                            <div class="col-12 col-md-4">
                                                <label class="form-label mb-1 text-white-50 small fw-bold text-uppercase">Buscar por RD</label>
                                                <div class="input-group shadow-sm">
                                                    <input type="text" name="search_rd" class="form-control border-0"
                                                        placeholder="Buscar por número RD..." value="{{ request('search_rd') }}">
                                                    <button type="submit" class="btn btn-dark d-flex align-items-center">
                                                        <span class="material-symbols-outlined fs-5">search</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label mb-1 text-white-50 small fw-bold text-uppercase">Buscar por Asunto</label>
                                                <div class="input-group shadow-sm">
                                                    <input type="text" name="search_asunto" class="form-control border-0"
                                                        placeholder="Buscar por texto de asunto..." value="{{ request('search_asunto') }}">
                                                    <button type="submit" class="btn btn-dark d-flex align-items-center">
                                                        <span class="material-symbols-outlined fs-5">search</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label mb-1 d-none d-md-block">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-dark shadow-sm w-100 d-flex align-items-center justify-content-center"
                                                        data-bs-toggle="modal" data-bs-target="#filterResolutionModal">
                                                        <span class="material-symbols-outlined me-1">tune</span> Filtros
                                                    </button>
                                                    @if (request()->filled('search') || request()->filled('search_rd') || request()->filled('search_asunto') || request()->filled('periodo') || request()->filled('resolucion_type_id') || request()->filled('asunto_type_id') || request()->filled('level_modality_id') || request()->filled('desde') || request()->filled('hasta'))
                                                        <a href="{{ route('resolucions.index') }}"
                                                            class="btn btn-light shadow-sm text-dark d-flex align-items-center justify-content-center"
                                                            title="Limpiar todos los filtros">
                                                            <span class="material-symbols-outlined">refresh</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if (request()->filled('search') || request()->filled('search_rd') || request()->filled('search_asunto') || request()->filled('periodo') || request()->filled('resolucion_type_id') || request()->filled('asunto_type_id') || request()->filled('level_modality_id') || request()->filled('desde') || request()->filled('hasta'))
                            <div class="px-3 py-3 border-bottom bg-light bg-opacity-50">
                                <form method="GET" action="{{ route('resolucions.index') }}" id="subfilterForm">
                                    <input type="hidden" name="search_rd" value="{{ request('search_rd') }}">
                                    <input type="hidden" name="search_asunto" value="{{ request('search_asunto') }}">
                                    <input type="hidden" name="periodo" value="{{ request('periodo') }}">
                                    <input type="hidden" name="resolucion_type_id" value="{{ request('resolucion_type_id') }}">
                                    <input type="hidden" name="asunto_type_id" value="{{ request('asunto_type_id') }}">
                                    <input type="hidden" name="level_modality_id" value="{{ request('level_modality_id') }}">
                                    <input type="hidden" name="desde" value="{{ request('desde') }}">
                                    <input type="hidden" name="hasta" value="{{ request('hasta') }}">

                                    <div class="row align-items-center">
                                        <div class="col-12 col-md-5">
                                            <div class="input-group input-group-sm shadow-sm">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <span class="material-symbols-outlined text-muted">filter_alt</span>
                                                </span>
                                                <input type="text" id="subfilter-input" name="search"
                                                    class="form-control border-start-0 border-end-0 ps-0"
                                                    placeholder="Buscar por interesado, DNI, procedencia..." value="{{ request('search') }}">
                                                <button type="submit" class="btn btn-dark d-flex align-items-center">
                                                    <span class="material-symbols-outlined fs-5">search</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-7 mt-2 mt-md-0">
                                            <small class="text-muted d-flex align-items-center gap-1" id="subfilter-info">
                                                <span class="material-symbols-outlined fs-6 text-info">info</span>
                                                Búsqueda general de texto completo en todas las páginas de resultados.
                                            </small>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Contenedor para "Sin resultados locales" -->
                            <div id="local-no-results" class="text-center py-5 d-none">
                                <span class="material-symbols-outlined fs-1 text-muted mb-2">search_off</span>
                                <p class="text-muted">No hay coincidencias en esta página.</p>
                                <button type="button" class="btn btn-warning btn-sm shadow-sm" id="btn-search-global">
                                    <span class="material-symbols-outlined">public</span> Buscar en todo el sistema
                                </button>
                            </div>

                            <!-- Vista Móvil -->
                            <section class="d-md-none p-3" aria-label="Lista de resoluciones para móviles">

                                @forelse ($resoluciones as $resolucion)
                                    <article class="card border-0 shadow-sm mb-3 overflow-hidden">
                                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $resolucion->type?->abreviacion ?? 'RD' }} {{ $resolucion->rd }}</span>
                                                        @if ($resolucion->document_path)
                                                            <a href="{{ route('resolucions.file.document', $resolucion) }}" target="_blank" class="text-danger d-flex align-items-center" title="Ver documento de resolución (PDF)">
                                                                <span class="material-symbols-outlined fs-5">picture_as_pdf</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div class="small text-muted d-flex align-items-center">
                                                        <span class="material-symbols-outlined fs-6 me-1" aria-hidden="true">calendar_today</span>
                                                        {{ $resolucion->formatted_fecha }}
                                                    </div>
                                                </div>
                                                @include('charges.partials.status-badge', ['status' => $resolucion->signature_status])
                                            </div>
                                        </div>
                                        <div class="card-body py-3">
                                            <div class="mb-3">
                                                <p class="text-muted small text-uppercase fw-bold mb-1">Interesado</p>
                                                <div class="fw-semibold text-dark text-truncate" title="{{ $resolucion->nombres_apellidos }}">
                                                    {{ $resolucion->nombres_apellidos }}
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <p class="text-muted small text-uppercase fw-bold mb-1">DNI</p>
                                                    <span class="text-dark">{{ $resolucion->dni ?? '---' }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted small text-uppercase fw-bold mb-1">Periodo</p>
                                                    <span class="badge bg-light text-dark border">{{ $resolucion->periodo }}</span>
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <p class="text-muted small text-uppercase fw-bold mb-1">Tipo de Asunto</p>
                                                    <span class="text-dark small fw-semibold d-block text-truncate" title="{{ $resolucion->asuntoType?->name ?? '---' }}">{{ $resolucion->asuntoType?->name ?? '---' }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted small text-uppercase fw-bold mb-1">Nivel/Modalidad</p>
                                                    <span class="text-dark small d-block text-truncate" title="{{ $resolucion->levelModality?->name ?? '---' }}">{{ $resolucion->levelModality?->name ?? '---' }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2 pt-2 border-top align-items-center">
                                                  @if ($resolucion->is_worked)
                                                      <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center py-1 px-2" title="Resolución Trabajada">
                                                          <span class="material-symbols-outlined fs-6 me-1">done_all</span> Trabajada
                                                      </span>
                                                  @elseif (Auth::user()->can('resolucion marcar trabajada'))
                                                      <form method="POST" action="{{ route('resolucions.work', $resolucion) }}" class="d-inline m-0 form-work-resolution">
                                                          @csrf
                                                          @method('PATCH')
                                                          <button type="submit" class="btn btn-outline-success btn-sm d-flex align-items-center" title="Marcar como trabajada" aria-label="Marcar como trabajada">
                                                              <span class="material-symbols-outlined fs-6 me-1" aria-hidden="true">task_alt</span> Trabajar
                                                          </button>
                                                      </form>
                                                  @endif

                                                <button type="button" class="btn btn-outline-info btn-sm btn-view-res-details d-flex align-items-center"
                                                    title="Ver detalles" aria-label="Ver detalles completos de la resolución {{ $resolucion->rd }}"
                                                    data-rd="{{ $resolucion->rd }}"
                                                    data-interesado="{{ $resolucion->nombres_apellidos }}"
                                                    data-dni="{{ $resolucion->dni }}"
                                                    data-resolucion_type="{{ $resolucion->type?->name ?? '---' }}"
                                                    data-asunto_type="{{ $resolucion->asuntoType?->name ?? '---' }}"
                                                    data-asunto="{{ $resolucion->asunto }}"
                                                    data-fecha="{{ $resolucion->formatted_fecha }}"
                                                    data-periodo="{{ $resolucion->periodo }}"
                                                    data-procedencia="{{ $resolucion->procedencia }}"
                                                    data-res-document-url="{{ $resolucion->file_document_url }}"
                                                    data-has-charge="{{ $resolucion->charge ? '1' : '0' }}"
                                                    data-has-signature="{{ $resolucion->charge?->has_signature ? '1' : '0' }}"
                                                    data-signature-url="{{ $resolucion->charge?->file_signature_url }}"
                                                    data-signer="{{ $resolucion->charge?->signature?->signer?->name ?? '' }} {{ $resolucion->charge?->signature?->signer?->last_name ?? '' }}"
                                                    data-titularidad="{{ $resolucion->charge?->signature?->titularidad ? '1' : '0' }}"
                                                    data-parentesco="{{ $resolucion->charge?->signature?->parentesco ?? '' }}"
                                                    data-titular-name="{{ $resolucion->nombres_apellidos }}"
                                                    data-has-evidence="{{ $resolucion->charge?->has_evidence ? '1' : '0' }}"
                                                    data-evidence-url="{{ $resolucion->charge?->file_evidence_url }}"
                                                    data-evidence-location='@json($resolucion->charge?->signature?->evidence_location)'
                                                    data-has-carta-poder="{{ $resolucion->charge?->has_carta_poder ? '1' : '0' }}"
                                                    data-carta-poder-url="{{ $resolucion->charge?->file_carta_poder_url }}">
                                                    <span class="material-symbols-outlined fs-6 me-1" aria-hidden="true">visibility</span> Detalles
                                                </button>

                                                @if ($resolucion->can_create_charge && $canCreateCharge)
                                                    @php
                                                        $interesadosList = $resolucion->naturalPeople->map(fn($p) => ['id' => $p->id, 'type' => 'Persona Natural', 'name' => "{$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}"])
                                                            ->merge($resolucion->legalEntities->map(fn($e) => ['id' => $e->id, 'type' => 'Persona Juridica', 'name' => $e->razon_social]))
                                                            ->merge($resolucion->users->map(fn($u) => ['id' => $u->id, 'type' => 'Trabajador UGEL', 'name' => "{$u->name} {$u->last_name}"]))
                                                            ->values();
                                                        $totalInteresados = $interesadosList->count();
                                                    @endphp
                                                    @if ($totalInteresados > 1)
                                                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center btn-trigger-select-interesado" 
                                                            data-action="{{ route('resolucions.charge.create', $resolucion) }}"
                                                            data-interesados='@json($interesadosList)'
                                                            data-rd="{{ $resolucion->rd }}"
                                                            aria-label="Elegir destinatario del cargo para la resolución {{ $resolucion->rd }}">
                                                            <span class="material-symbols-outlined fs-6 me-1" aria-hidden="true">group</span> Cargo
                                                        </button>
                                                    @else
                                                        <form method="POST" action="{{ route('resolucions.charge.create', $resolucion) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-primary btn-sm d-flex align-items-center" aria-label="Crear cargo para esta resolución">
                                                                <span class="material-symbols-outlined fs-6 me-1" aria-hidden="true">add</span> Cargo
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                <button type="button" class="btn btn-outline-success btn-sm btn-sign-resolution d-flex align-items-center"
                                                    title="Firmar" aria-label="Firmar cargo de la resolución {{ $resolucion->rd }}"
                                                    data-action="{{ $resolucion->charge ? route('charges.sign.store', $resolucion->charge) : '' }}"
                                                    data-charges='@json($resolucion->pending_charges_data)'
                                                    data-show-external="1" @disabled(!$resolucion->can_sign || !$canSignResolution)>
                                                    <span class="material-symbols-outlined fs-6 me-1" aria-hidden="true">history_edu</span> Firmar
                                                </button>

                                                @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                    <div class="ms-auto btn-group" role="group" aria-label="Acciones de edición y eliminación">
                                                        <button type="button" class="btn btn-outline-warning btn-sm btn-edit-resolution"
                                                            aria-label="Editar resolución"
                                                            data-action="{{ route('resolucions.update', $resolucion) }}"
                                                            data-rd="{{ $resolucion->rd }}"
                                                            data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('Y-m-d') : '' }}"
                                                            data-resolucion_type_id="{{ $resolucion->resolucion_type_id ?? '' }}"
                                                            data-asunto_type_id="{{ $resolucion->asunto_type_id ?? '' }}"
                                                            data-level_modality_id="{{ $resolucion->level_modality_id ?? '' }}"
                                                            data-procedencia="{{ $resolucion->procedencia ?? '' }}"
                                                            data-asunto="{{ $resolucion->asunto }}"
                                                            data-document-url="{{ $resolucion->file_document_url }}"
                                                            data-interesados="{{ $resolucion->naturalPeople->map(fn($p) => ['id' => $p->id, 'type' => 'Persona Natural', 'text' => "{$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}", 'identity' => $p->dni ?: $p->cedula])
                                                                ->merge($resolucion->legalEntities->map(fn($e) => ['id' => $e->id, 'type' => 'Persona Juridica', 'text' => $e->razon_social, 'identity' => $e->ruc]))
                                                                ->merge($resolucion->users->map(fn($u) => ['id' => $u->id, 'type' => 'Trabajador UGEL', 'text' => "{$u->name} {$u->last_name}", 'identity' => $u->dni]))
                                                                ->toJson() }}">
                                                            <span class="material-symbols-outlined fs-6" aria-hidden="true">edit</span>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-resolution"
                                                            aria-label="Eliminar resolución"
                                                            data-action="{{ route('resolucions.destroy', $resolucion) }}"
                                                            title="{{ $canDeleteResolution ? 'Eliminar' : 'Solo administradores' }}"
                                                            @disabled(!$canDeleteResolution)>
                                                            <span class="material-symbols-outlined fs-6" aria-hidden="true">delete</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <div class="text-center text-muted py-5 bg-white rounded-3 shadow-sm">
                                        <span class="material-symbols-outlined fs-1 d-block mb-2" aria-hidden="true">search_off</span>
                                        No se encontraron resoluciones
                                    </div>
                                @endforelse
                            </section>

                            <!-- Vista Escritorio -->
                            <section class="table-responsive d-none d-md-block" aria-label="Tabla de resoluciones para escritorio">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="--bs-table-bg: #e2eafc; --bs-table-color: #002855;">
                                        <tr class="border-bottom">
                                            <th class="ps-3 small fw-bold py-3" style="width: 50px;">#</th>
                                            <th class="small fw-bold py-3">Resolución</th>
                                            <th class="small fw-bold py-3">Interesado</th>
                                            <th class="small fw-bold py-3">Tipo de Asunto</th>
                                            <th class="small fw-bold py-3">Nivel / Modalidad</th>
                                            <th class="text-center small fw-bold py-3">Estado</th>
                                            @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                <th class="text-end pe-3 small fw-bold py-3">Acciones</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($resoluciones as $key => $resolucion)
                                            <tr>
                                                <td class="ps-3 text-muted small">
                                                    {{ ($resoluciones->currentPage() - 1) * $resoluciones->perPage() + $key + 1 }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="fw-bold text-primary">{{ $resolucion->type?->abreviacion ?? 'RD' }} {{ $resolucion->rd }}</div>
                                                        @if ($resolucion->document_path)
                                                            <a href="{{ route('resolucions.file.document', $resolucion) }}" target="_blank" class="text-danger d-flex align-items-center" title="Ver documento de resolución (PDF)">
                                                                <span class="material-symbols-outlined fs-5">picture_as_pdf</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div class="small text-muted d-flex align-items-center">
                                                        <span class="material-symbols-outlined fs-6 me-1" aria-hidden="true">calendar_today</span>
                                                        {{ $resolucion->formatted_fecha }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $resolucion->nombres_apellidos }}">
                                                        {{ $resolucion->nombres_apellidos }}
                                                    </div>
                                                    <div class="small text-muted">DNI: {{ $resolucion->dni ?? '---' }}</div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="{{ $resolucion->asuntoType?->name ?? '---' }}">
                                                        {{ $resolucion->asuntoType?->name ?? '---' }}
                                                    </div>
                                                    @if($resolucion->asuntoType?->description)
                                                        <div class="small text-muted text-truncate" style="max-width: 200px;" title="{{ $resolucion->asuntoType->description }}">
                                                            {{ $resolucion->asuntoType->description }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="text-muted small">
                                                        {{ $resolucion->levelModality?->name ?? '---' }}
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-light text-muted border small fw-normal">{{ $resolucion->procedencia }}</span>
                                                        <span class="badge bg-light text-muted border small fw-normal">{{ $resolucion->periodo }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @include('charges.partials.status-badge', ['status' => $resolucion->signature_status])
                                                </td>
                                                @if (!Auth::user()->hasRole('VISUALIZADOR'))
                                                    <td class="text-end pe-3">
                                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                                             @if ($resolucion->is_worked)
                                                                 <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center py-1 px-2" title="Resolución Trabajada">
                                                                     <span class="material-symbols-outlined fs-6 me-1">done_all</span> Trabajada
                                                                 </span>
                                                             @elseif (Auth::user()->can('resolucion marcar trabajada'))
                                                                 <form method="POST" action="{{ route('resolucions.work', $resolucion) }}" class="d-inline m-0 form-work-resolution">
                                                                     @csrf
                                                                     @method('PATCH')
                                                                     <button type="submit" class="btn btn-outline-success btn-sm d-flex align-items-center" title="Marcar como trabajada">
                                                                         <span class="material-symbols-outlined fs-5">task_alt</span>
                                                                     </button>
                                                                 </form>
                                                             @endif

                                                            {{-- Acción: Ver Detalles --}}
                                                            <button type="button" class="btn btn-outline-info btn-sm btn-view-res-details"
                                                                title="Ver detalles" aria-label="Ver detalles completos de la resolución {{ $resolucion->rd }}"
                                                                data-rd="{{ $resolucion->rd }}"
                                                                data-interesado="{{ $resolucion->nombres_apellidos }}"
                                                                data-dni="{{ $resolucion->dni }}"
                                                                data-resolucion_type="{{ $resolucion->type?->name ?? '---' }}"
                                                                data-asunto_type="{{ $resolucion->asuntoType?->name ?? '---' }}"
                                                                data-asunto="{{ $resolucion->asunto }}"
                                                                data-fecha="{{ $resolucion->formatted_fecha }}"
                                                                data-periodo="{{ $resolucion->periodo }}"
                                                                data-procedencia="{{ $resolucion->procedencia }}"
                                                                data-res-document-url="{{ $resolucion->file_document_url }}"
                                                                data-has-charge="{{ $resolucion->charge ? '1' : '0' }}"
                                                                data-has-signature="{{ $resolucion->charge?->has_signature ? '1' : '0' }}"
                                                                data-signature-url="{{ $resolucion->charge?->file_signature_url }}"
                                                                data-signer="{{ $resolucion->charge?->signature?->signer?->name ?? '' }} {{ $resolucion->charge?->signature?->signer?->last_name ?? '' }}"
                                                                data-titularidad="{{ $resolucion->charge?->signature?->titularidad ? '1' : '0' }}"
                                                                data-parentesco="{{ $resolucion->charge?->signature?->parentesco ?? '' }}"
                                                                data-titular-name="{{ $resolucion->nombres_apellidos }}"
                                                                data-has-evidence="{{ $resolucion->charge?->has_evidence ? '1' : '0' }}"
                                                                data-evidence-url="{{ $resolucion->charge?->file_evidence_url }}"
                                                                data-evidence-location='@json($resolucion->charge?->signature?->evidence_location)'
                                                                data-has-carta-poder="{{ $resolucion->charge?->has_carta_poder ? '1' : '0' }}"
                                                                data-carta-poder-url="{{ $resolucion->charge?->file_carta_poder_url }}">
                                                                <span class="material-symbols-outlined fs-5" aria-hidden="true">visibility</span>
                                                            </button>

                                                            {{-- Acción: Firmar --}}
                                                            <button type="button" class="btn btn-outline-success btn-sm btn-sign-resolution"
                                                                title="Firmar cargo" aria-label="Firmar documento de la resolución {{ $resolucion->rd }}"
                                                                data-action="{{ $resolucion->charge ? route('charges.sign.store', $resolucion->charge) : '' }}"
                                                                data-charges='@json($resolucion->pending_charges_data)'
                                                                data-show-external="1" @disabled(!$resolucion->can_sign || !$canSignResolution)>
                                                                <span class="material-symbols-outlined fs-5" aria-hidden="true">history_edu</span>
                                                            </button>
 
                                                             {{-- Menú de Más Acciones --}}
                                                             <div class="dropdown d-inline-block">
                                                                 <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Más opciones" data-bs-boundary="viewport" data-bs-config='{"popperConfig":{"strategy":"fixed"}}'>
                                                                     <span class="material-symbols-outlined align-middle fs-5">more_vert</span>
                                                                 </button>
                                                                 <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                                     @if ($resolucion->can_create_charge && $canCreateCharge)
                                                                         @php
                                                                             $interesadosList = $resolucion->naturalPeople->map(fn($p) => ['id' => $p->id, 'type' => 'Persona Natural', 'name' => "{$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}"])
                                                                                 ->merge($resolucion->legalEntities->map(fn($e) => ['id' => $e->id, 'type' => 'Persona Juridica', 'name' => $e->razon_social]))
                                                                                 ->merge($resolucion->users->map(fn($u) => ['id' => $u->id, 'type' => 'Trabajador UGEL', 'name' => "{$u->name} {$u->last_name}"]))
                                                                                 ->values();
                                                                             $totalInteresados = $interesadosList->count();
                                                                         @endphp
                                                                         <li>
                                                                             @if ($totalInteresados > 1)
                                                                                 <button type="button" class="dropdown-item d-flex align-items-center py-2 btn-trigger-select-interesado" 
                                                                                     data-action="{{ route('resolucions.charge.create', $resolucion) }}"
                                                                                     data-interesados='@json($interesadosList)'
                                                                                     data-rd="{{ $resolucion->rd }}"
                                                                                     title="Seleccionar destinatario para el cargo">
                                                                                     <span class="material-symbols-outlined me-2 fs-5 text-primary">group</span> Crear cargo
                                                                                 </button>
                                                                             @else
                                                                                 <form method="POST" action="{{ route('resolucions.charge.create', $resolucion) }}" class="d-block m-0">
                                                                                     @csrf
                                                                                     <button type="submit" class="dropdown-item d-flex align-items-center py-2" title="Crear cargo pendiente">
                                                                                         <span class="material-symbols-outlined me-2 fs-5 text-primary">add_notes</span> Crear cargo
                                                                                     </button>
                                                                                 </form>
                                                                             @endif
                                                                         </li>
                                                                     @endif
                                                                     <li>
                                                                         <button type="button" class="dropdown-item d-flex align-items-center py-2 btn-edit-resolution"
                                                                             data-action="{{ route('resolucions.update', $resolucion) }}"
                                                                             data-rd="{{ $resolucion->rd }}"
                                                                             data-fecha="{{ $resolucion->fecha ? $resolucion->fecha->format('Y-m-d') : '' }}"
                                                                             data-resolucion_type_id="{{ $resolucion->resolucion_type_id ?? '' }}"
                                                                             data-asunto_type_id="{{ $resolucion->asunto_type_id ?? '' }}"
                                                                             data-level_modality_id="{{ $resolucion->level_modality_id ?? '' }}"
                                                                             data-procedencia="{{ $resolucion->procedencia ?? '' }}"
                                                                             data-asunto="{{ $resolucion->asunto }}"
                                                                             data-document-url="{{ $resolucion->file_document_url }}"
                                                                            data-interesados="{{ $resolucion->naturalPeople->map(fn($p) => ['id' => $p->id, 'type' => 'Persona Natural', 'text' => "{$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}", 'identity' => $p->dni ?: $p->cedula])
                                                                                ->merge($resolucion->legalEntities->map(fn($e) => ['id' => $e->id, 'type' => 'Persona Juridica', 'text' => $e->razon_social, 'identity' => $e->ruc]))
                                                                                ->merge($resolucion->users->map(fn($u) => ['id' => $u->id, 'type' => 'Trabajador UGEL', 'text' => "{$u->name} {$u->last_name}", 'identity' => $u->dni]))
                                                                                ->toJson() }}" title="Editar">
                                                                            <span class="material-symbols-outlined me-2 fs-5 text-warning">edit</span> Editar resolución
                                                                        </button>
                                                                    </li>
                                                                    @if ($canDeleteResolution)
                                                                        <li><hr class="dropdown-divider"></li>
                                                                        <li>
                                                                            <button type="button" class="dropdown-item text-danger d-flex align-items-center py-2 btn-delete-resolution"
                                                                                data-action="{{ route('resolucions.destroy', $resolucion) }}"
                                                                                title="Eliminar resolución">
                                                                                <span class="material-symbols-outlined me-2 fs-5 text-danger">delete</span> Eliminar resolución
                                                                            </button>
                                                                        </li>
                                                                    @endif
                                                                </ul>
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
        <div class="modal fade" id="createResolutionModal" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Ingresar resolución</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    @include('resolucions.forms.create')
                </div>
            </div>
        </div>

        <!-- Modal: Editar resolucion -->
        <div class="modal fade" id="editResolutionModal" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Editar resolución</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    @include('resolucions.forms.edit')
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
        @include('resolucions.forms.create-charge')
        @include('resolucions.forms.view-details')

        <!-- Modal intermedia para elegir qué interesado firmará -->
        <div class="modal fade" id="selectSigneeModal" tabindex="-1" aria-labelledby="selectSigneeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center" id="selectSigneeModalLabel">
                            <span class="material-symbols-outlined me-2 fs-4">group</span>Seleccionar Firmante
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <p class="text-muted small">Esta resolución tiene múltiples cargos pendientes de firma. Por favor, seleccione quién firmará el documento en este momento:</p>
                        <div class="list-group shadow-sm" id="select_signee_list">
                            <!-- Los items de interesados se insertarán dinámicamente aquí -->
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Filtros Avanzados -->
        <div class="modal fade" id="filterResolutionModal" tabindex="-1" aria-labelledby="filterResolutionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center" id="filterResolutionModalLabel">
                            <span class="material-symbols-outlined me-2">tune</span>Filtros de Resoluciones
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <form method="GET" action="{{ route('resolucions.index') }}" id="modalFilterForm">
                        {{-- Conservar la búsqueda rápida y general --}}
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="search_rd" value="{{ request('search_rd') }}">
                        <input type="hidden" name="search_asunto" value="{{ request('search_asunto') }}">

                        <div class="modal-body px-4 py-3">
                            {{-- Filtro de Rango de Fechas --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Rango de Fechas (Resolución)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="filter_desde" class="form-label text-muted small mb-1">Desde</label>
                                        <input type="date" name="desde" id="filter_desde" class="form-control border-secondary-subtle" value="{{ request('desde') }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="filter_hasta" class="form-label text-muted small mb-1">Hasta</label>
                                        <input type="date" name="hasta" id="filter_hasta" class="form-control border-secondary-subtle" value="{{ request('hasta') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Filtro de Periodo --}}
                            <div class="mb-3">
                                <label for="filter_periodo" class="form-label fw-bold text-muted small text-uppercase">Periodo</label>
                                <select name="periodo" id="filter_periodo" class="form-select border-secondary-subtle">
                                    <option value="">Todos los periodos</option>
                                    @foreach ($periodos as $periodo)
                                        @if ($periodo !== null)
                                            <option value="{{ $periodo }}" {{ request('periodo') == $periodo ? 'selected' : '' }}>
                                                {{ $periodo }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro de Tipo de Resolución --}}
                            <div class="mb-3">
                                <label for="filter_resolution_type" class="form-label fw-bold text-muted small text-uppercase">Tipo de Resolución</label>
                                <select name="resolucion_type_id" id="filter_resolution_type" class="form-select border-secondary-subtle">
                                    <option value="">Todos los tipos...</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}" {{ request('resolucion_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro de Tipo de Asunto --}}
                            <div class="mb-3">
                                <label for="filter_asunto_type" class="form-label fw-bold text-muted small text-uppercase">Tipo de Asunto</label>
                                <select name="asunto_type_id" id="filter_asunto_type" class="form-select border-secondary-subtle" disabled data-selected="{{ request('asunto_type_id') }}">
                                    <option value="">Seleccione tipo de resolución primero...</option>
                                </select>
                            </div>

                            {{-- Filtro de Modalidad / Nivel --}}
                            <div class="mb-3">
                                <label for="filter_level_modality" class="form-label fw-bold text-muted small text-uppercase">Modalidad / Nivel</label>
                                <select name="level_modality_id" id="filter_level_modality" class="form-select border-secondary-subtle">
                                    <option value="">Todas las modalidades...</option>
                                    @foreach ($level_modalities as $modality)
                                        <option value="{{ $modality->id }}" {{ request('level_modality_id') == $modality->id ? 'selected' : '' }}>
                                            {{ $modality->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top py-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                            <a href="{{ route('resolucions.index') }}" class="btn btn-light border btn-sm px-3">Limpiar Filtros</a>
                            <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                                <span class="material-symbols-outlined align-middle fs-6 me-1">filter_alt</span>Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Seleccionar Interesado para Cargo -->
        <div class="modal fade" id="selectInteresadoModal" tabindex="-1" aria-labelledby="selectInteresadoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center" id="selectInteresadoModalLabel">
                            <span class="material-symbols-outlined me-2 fs-4">group</span>Seleccionar Destinatario
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" id="selectInteresadoForm">
                        @csrf
                        <input type="hidden" name="interesado_id" id="modal_select_interesado_id">
                        <input type="hidden" name="interesado_type" id="modal_select_interesado_type">
                        
                        <div class="modal-body p-4">
                            <p class="text-muted small mb-3">La resolución <strong id="modal_select_interesado_rd" class="text-dark"></strong> contiene múltiples interesados. Por favor, seleccione para cuál de ellos desea registrar el cargo de notificación:</p>
                            <div class="list-group shadow-sm" id="modal_select_interesados_list">
                                {{-- Cargados dinámicamente --}}
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top py-2 px-4">
                            <button type="button" class="btn btn-outline-secondary px-4 btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    console.info('[Resolucions index] Inicializando autoapertura por errores detectados en el servidor.');
                    const keys = {!! json_encode($errors->keys()) !!};
                    console.log('[Resolucions index] Claves de error del backend:', keys);

                    const chargeErrors = [
                        'asunto',
                        'resolucion_ids',
                        'destinatarios',
                        'document_file',
                        'document_date'
                    ];
                    const hasChargeError = keys.some(k => chargeErrors.includes(k) || k.startsWith('destinatarios.'));

                    if (hasChargeError) {
                        console.log('[Resolucions index] Detectado error en cargos. Reabriendo #createChargeModal.');
                        const modalEl = document.getElementById('createChargeModal');
                        if (modalEl) {
                            bootstrap.Modal.getOrCreateInstance(modalEl).show();
                        }
                    } else {
                        // Error de resolución. Verificar origen (create o edit)
                        const origin = "{{ old('action_origin') }}";
                        console.log('[Resolucions index] Detectado error en resoluciones. Origen de acción:', origin);

                        if (origin === 'create') {
                            console.log('[Resolucions index] Reabriendo #createResolutionModal.');
                            const modalEl = document.getElementById('createResolutionModal');
                            if (modalEl) {
                                bootstrap.Modal.getOrCreateInstance(modalEl).show();
                            }
                        } else if (origin === 'edit') {
                            const editId = "{{ old('edit_resolucion_id') }}";
                            console.log(`[Resolucions index] Reabriendo modal de edición para ID: ${editId}`);
                            if (editId) {
                                // Buscar el botón de editar correspondiente en la tabla y disparar su evento click
                                const btnEdit = document.querySelector(`.btn-edit-resolution[data-id="${editId}"]`);
                                if (btnEdit) {
                                    console.log('[Resolucions index] Botón de edición encontrado en tabla. Ejecutando click simulado.');
                                    btnEdit.click();
                                } else {
                                    console.warn(`[Resolucions index] No se encontró el botón de edición para ID: ${editId} en la tabla.`);
                                }
                            }
                        }
                    }
                });
            </script>
        @endif
    @endsection
