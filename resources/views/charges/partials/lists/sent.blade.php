<div class="tab-pane fade {{ $active ?? false ? 'show active' : '' }}" id="sent-tab-pane" role="tabpanel"
    aria-labelledby="sent-tab" tabindex="0">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-white">Cargos enviados</h5>
                    <span class="badge bg-light text-dark">{{ $sentTotal }}</span>
                    <span class="badge bg-warning text-dark">Pendientes: {{ $sentPending }}</span>
                    <span class="badge bg-primary">Firmados: {{ $sentSigned }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="d-md-none d-flex gap-2">
                        <button class="btn btn-light" type="button" data-bs-toggle="collapse"
                            data-bs-target="#sent-filters">
                            <i class="fa-solid fa-filter me-1"></i> Filtros
                        </button>
                        <button class="btn btn-light" type="button" data-bs-toggle="collapse"
                            data-bs-target="#sent-pdf">
                            <i class="fa-solid fa-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                    <div class="d-none d-md-flex gap-2 align-items-center">
                        @include('charges.forms.filter')
                        <form action="{{ route('charges.reports.sent') }}" method="GET">
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if (request('signature_status'))
                                <input type="hidden" name="signature_status" value="{{ request('signature_status') }}">
                            @endif
                            @if ($sentPeriod)
                                <input type="hidden" name="period" value="{{ $sentPeriod }}">
                            @endif
                            <button class="btn btn-light" type="submit">
                                <i class="fa-solid fa-file-pdf me-1"></i> Reporte PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros y PDF para móvil --}}
        <div class="collapse d-md-none px-3 pt-3 pb-3" id="sent-filters">
            @include('charges.forms.filter')
        </div>
        <div class="collapse d-md-none px-3 pt-3 pb-3" id="sent-pdf">
            <form action="{{ route('charges.reports.sent') }}" method="GET">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if (request('signature_status'))
                    <input type="hidden" name="signature_status" value="{{ request('signature_status') }}">
                @endif
                @if ($sentPeriod)
                    <input type="hidden" name="period" value="{{ $sentPeriod }}">
                @endif
                <button class="btn btn-light w-100" type="submit">
                    <i class="fa-solid fa-file-pdf me-1"></i> Reporte PDF
                </button>
            </form>
        </div>

        <div class="card-body p-0">
            {{-- VISTA MÓVIL --}}
            <div class="d-md-none p-3">
                @forelse ($sentChargesFiltered as $charge)
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="small text-muted text-uppercase">Cargo</div>
                                    <div class="fw-semibold">#{{ $charge->n_charge }}</div>
                                    <div class="small text-muted mt-1">
                                        {{ optional($charge->created_at)->format('Y-m-d H:i') }}</div>
                                </div>
                                <div>
                                    @include('charges.partials.status-badge', [
                                        'status' => $charge->signature?->signature_status,
                                    ])
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="small text-muted text-uppercase">Interesado</div>
                                <div class="fw-semibold">{{ $charge->interesado_label }}</div>
                                <div class="small text-muted">{{ $charge->tipo_interesado }}</div>
                            </div>
                            <div class="mt-2">
                                <div class="small text-muted text-uppercase">Asunto</div>
                                <div class="fw-semibold text-truncate" style="max-width: 250px;">{{ $charge->asunto }}
                                </div>
                            </div>
                            <div class="mt-3">
                                @include('charges.partials.item-actions', [
                                    'charge' => $charge,
                                    'canEdit' => true,
                                ])
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">No hay cargos enviados.</div>
                @endforelse
            </div>

            {{-- VISTA ESCRITORIO --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>No. cargo</th>
                            <th>Fecha</th>
                            <th>Interesado</th>
                            <th>Asunto</th>
                            <th>Asignado a</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sentChargesFiltered as $key => $charge)
                            <tr>
                                <td class="fw-semibold text-muted">{{ $key + 1 }}</td>
                                <td>{{ $charge->n_charge }} ({{ $charge->charge_period }})</td>
                                <td>
                                    <div class="fw-semibold">{{ optional($charge->created_at)->format('Y-m-d') }}</div>
                                    <div class="small text-muted">{{ optional($charge->created_at)->format('H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $charge->interesado_label }}</div>
                                    <div class="small text-muted">{{ $charge->tipo_interesado }}</div>
                                </td>
                                <td style="max-width: 200px;" class="text-truncate" title="{{ $charge->asunto }}">
                                    {{ $charge->asunto }}</td>
                                <td>
                                    @if ($charge->signature?->assignedTo)
                                        <span
                                            class="fw-semibold text-capitalize">{{ strtolower($charge->signature->assignedTo->name) }}</span>
                                    @else
                                        <span class="text-muted">No asignado</span>
                                    @endif
                                </td>
                                <td>@include('charges.partials.status-badge', [
                                    'status' => $charge->signature?->signature_status,
                                ])</td>
                                <td class="text-end">
                                    @include('charges.partials.item-actions', [
                                        'charge' => $charge,
                                        'canEdit' => true,
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No hay cargos enviados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
