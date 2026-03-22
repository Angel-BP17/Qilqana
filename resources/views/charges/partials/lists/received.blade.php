<div class="tab-pane fade {{ $active ?? false ? 'show active' : '' }}" id="received-tab-pane" role="tabpanel"
    aria-labelledby="received-tab" tabindex="0">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-white">Cargos recibidos</h5>
                    <span class="badge bg-light text-dark">{{ $receivedTotal }}</span>
                    <span class="badge bg-warning text-dark">Pendientes: {{ $receivedPending }}</span>
                    <span class="badge bg-primary">Firmados: {{ $receivedSigned }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="d-md-none d-flex gap-2">
                        <button class="btn btn-light" type="button" data-bs-toggle="collapse"
                            data-bs-target="#received-filters">
                            <i class="fa-solid fa-filter me-1"></i> Filtros
                        </button>
                    </div>
                    <div class="d-none d-md-flex gap-2 align-items-center">
                        <form class="d-flex flex-wrap gap-2" action="{{ route('charges.index') }}" method="GET">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa-solid fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" name="received_search"
                                        placeholder="No de cargo, RUC, DNI..." value="{{ request('received_search') }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <select name="received_period" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos los periodos</option>
                                    @foreach ($periodOptions ?? [] as $period)
                                        <option value="{{ $period }}" @selected(($receivedPeriod ?? request('received_period')) === $period)>
                                            {{ $period }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filtrar</button>
                            </div>
                        </form>
                        <form action="{{ route('charges.reports.received') }}" method="GET">
                            @if (request('received_search'))
                                <input type="hidden" name="received_search" value="{{ request('received_search') }}">
                            @endif
                            @if ($receivedPeriod)
                                <input type="hidden" name="received_period" value="{{ $receivedPeriod }}">
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
                @forelse ($receivedCharges as $charge)
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="small text-muted">#{{ $charge->n_charge }}</div>
                                    <div class="fw-semibold">{{ $charge->interesado_label }}</div>
                                    <div class="small text-muted">{{ $charge->asunto }}</div>
                                </div>
                                @include('charges.partials.status-badge', [
                                    'status' => $charge->signature?->signature_status,
                                ])
                            </div>
                            <div class="mt-3">
                                @include('charges.partials.item-actions', [
                                    'charge' => $charge,
                                    'canSign' => true,
                                    'canReject' => true,
                                ])
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">No hay cargos recibidos.</div>
                @endforelse
            </div>

            {{-- VISTA ESCRITORIO --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>No. cargo</th>
                            <th>Interesado</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($receivedCharges as $key => $charge)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $charge->n_charge }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $charge->interesado_label }}</div>
                                    <div class="small text-muted">{{ $charge->tipo_interesado }}</div>
                                </td>
                                <td style="max-width: 250px;" class="text-truncate">{{ $charge->asunto }}</td>
                                <td>@include('charges.partials.status-badge', [
                                    'status' => $charge->signature?->signature_status,
                                ])</td>
                                <td class="text-end">
                                    @include('charges.partials.item-actions', [
                                        'charge' => $charge,
                                        'canSign' => true,
                                        'canReject' => true,
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No hay cargos recibidos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
