<div class="tab-pane fade {{ $active ?? false ? 'show active' : '' }}" id="resolution-tab-pane" role="tabpanel"
    aria-labelledby="resolution-tab" tabindex="0">
    @php
        \Illuminate\Support\Facades\Log::info('Blade::resolution.blade.php - Renderizando lista', [
            'count' => $resolutionCharges->count(),
        ]);
    @endphp
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
                                        <i class="fa-solid fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" name="resolution_search"
                                        placeholder="No. cargo, RD, nombres..."
                                        value="{{ request('resolution_search') }}">
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-light" type="submit"><i class="fas fa-filter"></i>
                                    Filtrar</button>
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
                                <i class="fa-solid fa-file-pdf me-1"></i> Reporte PDF
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
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="small text-muted">Cargo #{{ $charge->n_charge }}</div>
                                    <div class="fw-semibold">RD: {{ $charge->resolucion?->rd }}</div>
                                    <div class="small text-muted">{{ $charge->resolucion?->periodo }}</div>
                                </div>
                                @include('charges.partials.status-badge', [
                                    'status' => $charge->signature?->signature_status,
                                ])
                            </div>
                            <div class="mt-2">
                                <div class="small text-muted">Interesado: {{ $charge->resolucion?->nombres_apellidos }}
                                </div>
                            </div>
                            <div class="mt-3">
                                @include('charges.partials.item-actions', ['charge' => $charge])
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">No hay cargos de resoluciones.</div>
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
                                <td>{{ $charge->resolucion?->nombres_apellidos }}</td>
                                <td>{{ $charge->resolucion?->dni }}</td>
                                <td style="max-width: 200px;" class="text-truncate">{{ $charge->asunto }}</td>
                                <td>@include('charges.partials.status-badge', [
                                    'status' => $charge->signature?->signature_status,
                                ])</td>
                                <td class="text-end">
                                    @include('charges.partials.item-actions', ['charge' => $charge])
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
