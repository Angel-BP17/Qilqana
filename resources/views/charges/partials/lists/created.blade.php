<div class="tab-pane fade {{ ($active ?? false) ? 'show active' : '' }}" id="created-tab-pane" role="tabpanel" aria-labelledby="created-tab" tabindex="0">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-white">Cargos creados</h5>
                    <span class="badge bg-light text-dark">{{ $createdTotal }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="d-none d-md-flex gap-2 align-items-center">
                        <form class="d-flex flex-wrap gap-2" action="{{ route('charges.index') }}" method="GET">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <span class="material-symbols-outlined text-muted">search</span>
                                    </span>
                                    <input type="text" class="form-control border-start-0" name="created_search"
                                        placeholder="No de cargo, RUC, DNI..." value="{{ request('created_search') }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <select name="created_period" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos los periodos</option>
                                    @foreach ($periodOptions ?? [] as $period)
                                        <option value="{{ $period }}" @selected(($createdPeriod ?? request('created_period')) === $period)>
                                            {{ $period }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-light" type="submit"><span class="material-symbols-outlined">filter_alt</span> Filtrar</button>
                            </div>
                        </form>
                        <form action="{{ route('charges.reports.created') }}" method="GET">
                            @if (request('created_search')) <input type="hidden" name="created_search" value="{{ request('created_search') }}"> @endif
                            @if ($createdPeriod) <input type="hidden" name="created_period" value="{{ $createdPeriod }}"> @endif
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
                @forelse ($createdCharges as $charge)
                    <div class="card border-0 shadow-sm mb-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">Cargo #{{ $charge->n_charge }} ({{ $charge->charge_period }})</span>
                                    <div class="small text-muted d-flex align-items-center">
                                        <span class="material-symbols-outlined fs-6 me-1">calendar_today</span>
                                        {{ optional($charge->created_at)->format('d/m/Y H:i') }}
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
                                <div class="small text-muted">{{ $charge->tipo_interesado }}</div>
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
                        No hay cargos creados.
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
                            <th>Interesado</th>
                            <th>Tipo</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($createdCharges as $key => $charge)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $charge->n_charge }}</td>
                                <td style="max-width: 250px;">
                                    <div class="fw-semibold text-truncate" title="{{ $charge->interesado_label }}">
                                        {{ $charge->interesado_label }}
                                    </div>
                                </td>
                                <td>{{ $charge->tipo_interesado }}</td>
                                <td style="max-width: 250px;" class="text-truncate">{{ $charge->asunto }}</td>
                                <td>@include('charges.partials.status-badge', ['status' => $charge->signature?->signature_status])</td>
                                <td class="text-end">
                                    @include('charges.partials.item-actions', ['charge' => $charge, 'canSign' => true])
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">No hay cargos creados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
