<div class="tab-pane fade {{ $active ?? false ? 'show active' : '' }}" id="resolution-tab-pane" role="tabpanel"
    aria-labelledby="resolution-tab" tabindex="0">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-white">Cargos de resoluciones</h5>
                    <span class="badge bg-light text-dark">{{ $resolutionCharges->count() }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="d-none d-md-flex gap-2 align-items-center">
                        <form class="d-flex flex-wrap gap-2 row" action="{{ route('charges.index') }}" method="GET">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <span class="material-symbols-outlined text-muted">search</span>
                                    </span>
                                    <input type="text" class="form-control border-start-0" name="resolution_search"
                                        placeholder="No. cargo, RD, nombres..." value="{{ request('resolution_search') }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <select name="resolution_period" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos los periodos</option>
                                    @foreach ($periodOptions ?? [] as $period)
                                        <option value="{{ $period }}" @selected(($resolutionPeriod ?? request('resolution_period')) === $period)>
                                            {{ $period }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-light" type="submit"><span class="material-symbols-outlined">filter_alt</span> Filtrar</button>
                            </div>
                        </form>
                        <form action="{{ route('charges.reports.resolution') }}" method="GET">
                            @if (request('resolution_search'))
                                <input type="hidden" name="resolution_search"
                                    value="{{ request('resolution_search') }}">
                            @endif
                            @if ($resolutionPeriod)
                                <input type="hidden" name="resolution_period" value="{{ $resolutionPeriod }}">
                            @endif
                            <button class="btn btn-light" type="submit">
                                <span class="material-symbols-outlined me-1">picture_as_pdf</span> Reporte PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- VISTA MÓVIL --}}
            <div class="d-md-none p-3">
                @forelse ($resolutionCharges as $charge)
                    <div class="card border-0 shadow-sm mb-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">RD {{ $charge->resolucion?->rd }}</span>
                                    <div class="small text-muted d-flex align-items-center">
                                        <span class="material-symbols-outlined fs-6 me-1">receipt_long</span>
                                        Cargo #{{ $charge->n_charge }}
                                    </div>
                                </div>
                                @include('charges.partials.status-badge', [
                                    'status' => $charge->signature?->signature_status,
                                ])
                            </div>
                        </div>
                        <div class="card-body py-3">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Interesado</label>
                                <div class="fw-semibold text-dark text-truncate" title="{{ $charge->resolucion?->nombres_apellidos }}">
                                    {{ $charge->resolucion?->nombres_apellidos }}
                                </div>
                                <div class="small text-muted">DNI: {{ $charge->resolucion?->dni ?? '---' }}</div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block">Periodo</label>
                                    <span class="badge bg-light text-dark border small fw-normal">{{ $charge->resolucion?->periodo }}</span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block">Procedencia</label>
                                    <span class="small text-dark">{{ $charge->resolucion?->procedencia ?? '---' }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Asunto</label>
                                <div class="small text-dark lh-sm">{{ Str::limit($charge->asunto, 100) }}</div>
                            </div>

                            <div class="pt-2 border-top">
                                @include('charges.partials.item-actions', [
                                    'charge' => $charge,
                                    'canSign' => true,
                                ])
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5 bg-white rounded-3 shadow-sm">
                        <span class="material-symbols-outlined fs-1 d-block mb-2">inbox</span>
                        No hay cargos de resoluciones.
                    </div>
                @endforelse
            </div>

            {{-- VISTA ESCRITORIO --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>No. cargo</th>
                            <th>RD</th>
                            <th>Período</th>
                            <th>Nombres y apellidos</th>
                            <th>DNI</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($resolutionCharges as $key => $charge)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $charge->n_charge }}</td>
                                <td class="fw-bold">{{ $charge->resolucion?->rd }}</td>
                                <td>{{ $charge->resolucion?->periodo }}</td>
                                <td style="max-width: 250px;">
                                    <div class="fw-semibold text-truncate" title="{{ $charge->resolucion?->nombres_apellidos }}">
                                        {{ $charge->resolucion?->nombres_apellidos }}
                                    </div>
                                </td>
                                <td>{{ $charge->resolucion?->dni }}</td>
                                <td style="max-width: 200px;" class="text-truncate">{{ $charge->asunto }}</td>
                                <td>@include('charges.partials.status-badge', [
                                    'status' => $charge->signature?->signature_status,
                                ])</td>
                                <td class="text-end">
                                    @include('charges.partials.item-actions', [
                                        'charge' => $charge,
                                        'canSign' => true,
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No hay cargos de resoluciones.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
