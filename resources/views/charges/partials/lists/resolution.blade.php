<div class="tab-pane fade {{ $active ?? false ? 'show active' : '' }}" id="resolution-tab-pane" role="tabpanel"
    aria-labelledby="resolution-tab" tabindex="0">
    <div class="card border-0 shadow-sm mb-4">
        <x-card-header title="Cargos de resoluciones" :badge="$resolutionCharges->count()" colSize="md">
            <form class="d-flex flex-wrap gap-2 flex-grow-1 flex-md-grow-0 justify-content-md-end" action="{{ route('charges.index') }}" method="GET">
                <div class="flex-grow-1" style="min-width: 200px; max-width: 400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <span class="material-symbols-outlined text-muted">search</span>
                        </span>
                        <input type="text" class="form-control border-start-0" name="resolution_search"
                            placeholder="No. cargo, RD, nombres..." value="{{ request('resolution_search') }}">
                    </div>
                </div>
                <div style="width: 120px;">
                    <select name="resolution_period" class="form-select" onchange="this.form.submit()">
                        <option value="">Periodo</option>
                        @foreach ($periodOptions ?? [] as $period)
                            <option value="{{ $period }}" @selected(($resolutionPeriod ?? request('resolution_period')) === $period)>
                                {{ $period }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-light" type="submit" title="Filtrar">
                    <span class="material-symbols-outlined">filter_alt</span>
                    <span class="d-md-none d-lg-inline">Filtrar</span>
                </button>
            </form>
            <form action="{{ route('charges.reports.resolution') }}" method="GET" target="_blank" class="flex-grow-1 flex-md-grow-0">
                @if (request('resolution_search'))
                    <input type="hidden" name="resolution_search" value="{{ request('resolution_search') }}">
                @endif
                @if ($resolutionPeriod)
                    <input type="hidden" name="resolution_period" value="{{ $resolutionPeriod }}">
                @endif
                <button class="btn btn-light w-100" type="submit" @disabled($resolutionCharges->isEmpty())>
                    <span class="material-symbols-outlined me-1">picture_as_pdf</span>
                    <span class="d-none d-sm-inline">Reporte PDF</span>
                    <span class="d-sm-none">PDF</span>
                </button>
            </form>
        </x-card-header>

        <div class="card-body p-0">
            {{-- VISTA MÓVIL --}}
            <div class="d-md-none p-3">
                @forelse ($resolutionCharges as $charge)
                    <div class="card border-0 shadow-sm mb-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
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
                                <div class="fw-semibold text-dark text-truncate" title="{{ $charge->interesado_label }}">
                                    {{ $charge->interesado_label }}
                                </div>
                                <div class="small text-muted">Identidad: {{ $charge->interesado_dni }}</div>
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
                            <th>Período</th>
                            <th>Nombres y apellidos/Razón social</th>
                            <th>DNI/RUC</th>
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
                                <td>{{ $charge->resolucion?->periodo }}</td>
                                <td style="max-width: 200px;">
                                    <div class="fw-semibold text-truncate" title="{{ $charge->interesado_label }}">
                                        {{ $charge->interesado_label }}
                                    </div>
                                </td>
                                <td>{{ $charge->interesado_dni }}</td>
                                <td style="max-width: 450px;" class="text-truncate" title="{{ $charge->asunto }}">{{ $charge->asunto }}</td>
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
