    <div class="tab-pane fade {{ ($active ?? false) ? 'show active' : '' }}" id="received-tab-pane" role="tabpanel" aria-labelledby="received-tab"
        tabindex="0">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info border-0 py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-white">Cargos recibidos</h5>
                        <span class="badge bg-light text-dark">{{ $receivedTotal }}</span>
                        <span class="badge bg-warning text-dark">Pendientes: {{ $receivedPending }}</span>
                        <span class="badge bg-primary">Firmados: {{ $receivedSigned }}</span>
                        @if ($receivedRejected > 0)
                            <span class="badge bg-danger">Rechazados: {{ $receivedRejected }}</span>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="d-md-none d-flex gap-2">
                            <button class="btn btn-light" type="button" data-bs-toggle="collapse"
                                data-bs-target="#received-filters">
                                <i class="fa-solid fa-filter me-1"></i> Filtros
                            </button>
                            <button class="btn btn-light" type="button" data-bs-toggle="collapse"
                                data-bs-target="#received-pdf">
                                <i class="fa-solid fa-file-pdf me-1"></i> PDF
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
                                            placeholder="No de cargo, RUC, DNI, nombres o asunto"
                                            value="{{ request('received_search') }}">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <select name="received_signature_status" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">Todos</option>
                                        <option value="pendiente" @selected(request('received_signature_status') === 'pendiente')>
                                            Pendientes</option>
                                        <option value="firmado" @selected(request('received_signature_status') === 'firmado')>Firmados
                                        </option>
                                        <option value="rechazado" @selected(request('received_signature_status') === 'rechazado')>
                                            Rechazados</option>
                                    </select>
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
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i>
                                        Filtrar</button>
                                </div>
                            </form>
                            <form action="{{ route('charges.reports.received') }}" method="GET">
                                @if (request('received_search'))
                                    <input type="hidden" name="received_search"
                                        value="{{ request('received_search') }}">
                                @endif
                                @if (request('received_signature_status'))
                                    <input type="hidden" name="received_signature_status"
                                        value="{{ request('received_signature_status') }}">
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
            <div class="collapse d-md-none px-3 pt-3 pb-3" id="received-filters">
                <form class="d-flex flex-wrap gap-2" action="{{ route('charges.index') }}" method="GET">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" name="received_search"
                                placeholder="No de cargo, RUC, DNI, nombres o asunto"
                                value="{{ request('received_search') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <select name="received_signature_status" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="pendiente" @selected(request('received_signature_status') === 'pendiente')>
                                Pendientes</option>
                            <option value="firmado" @selected(request('received_signature_status') === 'firmado')>Firmados
                            </option>
                            <option value="rechazado" @selected(request('received_signature_status') === 'rechazado')>
                                Rechazados</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <select name="received_period" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos los periodos</option>
                            @foreach ($periodOptions ?? [] as $period)
                                <option value="{{ $period }}" @selected(($receivedPeriod ?? request('received_period')) === $period)>
                                    {{ $period }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100" type="submit"><i class="fas fa-filter me-1"></i>
                            Filtrar</button>
                    </div>
                </form>
            </div>
            <div class="collapse d-md-none px-3 pt-3 pb-3" id="received-pdf">
                <form action="{{ route('charges.reports.received') }}" method="GET">
                    @if (request('received_search'))
                        <input type="hidden" name="received_search" value="{{ request('received_search') }}">
                    @endif
                    @if (request('received_signature_status'))
                        <input type="hidden" name="received_signature_status"
                            value="{{ request('received_signature_status') }}">
                    @endif
                    @if ($receivedPeriod)
                        <input type="hidden" name="received_period" value="{{ $receivedPeriod }}">
                    @endif
                    <button class="btn btn-light w-100" type="submit">
                        <i class="fa-solid fa-file-pdf me-1"></i> Reporte PDF
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="d-md-none p-3">
                    @forelse ($receivedCharges as $key => $charge)
                        @php
                            $signatureContent = null;
                            if (
                                $charge->signature?->signature_root &&
                                \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                    $charge->signature->signature_root,
                                )
                            ) {
                                $signatureContent = \Illuminate\Support\Facades\Storage::disk('local')->get(
                                    $charge->signature->signature_root,
                                );
                            }
                            $cartaPoderData = null;
                            if (
                                $charge->signature?->carta_poder_path &&
                                \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                    $charge->signature->carta_poder_path,
                                )
                            ) {
                                $cartaPath = $charge->signature->carta_poder_path;
                                $extension = strtolower(pathinfo($cartaPath, PATHINFO_EXTENSION));
                                $mimeType = match ($extension) {
                                    'jpg', 'jpeg' => 'image/jpeg',
                                    'png' => 'image/png',
                                    'pdf' => 'application/pdf',
                                    default => 'application/octet-stream',
                                };
                                $content = \Illuminate\Support\Facades\Storage::disk('local')->get($cartaPath);
                                $cartaPoderData = 'data:' . $mimeType . ';base64,' . base64_encode($content);
                            }
                            $evidenceData = null;
                            if (
                                $charge->signature?->evidence_root &&
                                \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                    $charge->signature->evidence_root,
                                )
                            ) {
                                $evidencePath = $charge->signature->evidence_root;
                                $extension = strtolower(pathinfo($evidencePath, PATHINFO_EXTENSION));
                                $mimeType = match ($extension) {
                                    'jpg', 'jpeg' => 'image/jpeg',
                                    'png' => 'image/png',
                                    default => 'application/octet-stream',
                                };
                                $content = \Illuminate\Support\Facades\Storage::disk('local')->get($evidencePath);
                                $evidenceData = 'data:' . $mimeType . ';base64,' . base64_encode($content);
                            }
                            $interesadoLabel =
                                $charge->tipo_interesado === 'Persona Juridica'
                                    ? ($charge->legalEntity?->razon_social ?:
                                    $charge->legalEntity?->ruc ?:
                                    '???')
                                    : (trim(
                                        ($charge->naturalPerson?->nombres ?? '') .
                                            ' ' .
                                            ($charge->naturalPerson?->apellidos ?? ''),
                                    ) ?:
                                    ($charge->naturalPerson?->dni ?:
                                    '???'));
                        @endphp
                        <div class="card border-0 shadow-sm mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="small text-muted text-uppercase">Cargo</div>
                                        <div class="fw-semibold">#{{ $charge->n_charge }}</div>
                                        <div class="small text-muted text-uppercase mt-1">Periodo</div>
                                        <div class="fw-semibold">{{ $charge->charge_period ?? '?' }}</div>
                                    </div>
                                    <div>
                                        @if ($charge->signature?->signature_status === 'firmado')
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-circle-check me-1"></i>Firmado
                                            </span>
                                        @elseif ($charge->signature?->signature_status === 'rechazado')
                                            <span class="badge bg-danger">
                                                <i class="fa-solid fa-circle-xmark me-1"></i>Rechazado
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fa-solid fa-hourglass-half me-1"></i>Pendiente
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="small text-muted text-uppercase mt-2">Enviado por</div>
                                <div class="fw-semibold">{{ $charge->user->name ?? 'Ã¢â‚¬â€' }}</div>
                                <div class="mt-2">
                                    <div class="small text-muted text-uppercase">Interesado</div>
                                    <div class="fw-semibold">{{ $interesadoLabel }}</div>
                                    <div class="small text-muted">{{ $charge->tipo_interesado }}</div>
                                </div>
                                <div class="mt-2">
                                    <div class="small text-muted text-uppercase">Asunto</div>
                                    <div class="fw-semibold">{{ $charge->asunto }}</div>
                                </div>
                                <div class="mt-3 d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm btn-sign-charge"
                                        title="Firmar" data-action="{{ route('charges.sign.store', $charge) }}"
                                        data-charge='@json($charge)'
                                        data-signature='@json($signatureContent ?? '')'
                                        data-signer="{{ $charge->signature?->signer?->name ?? '' }}"
                                        @disabled($charge->signature?->signature_status !== 'pendiente')>
                                        <i class="fa-solid fa-signature"></i> Firmar
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-reject-charge"
                                        title="Rechazar" data-action="{{ route('charges.reject', $charge) }}"
                                        data-charge-id="{{ $charge->id }}" @disabled($charge->signature?->signature_status !== 'pendiente')>
                                        <i class="fa-solid fa-ban"></i> Rechazar
                                    </button>
                                    @include('charges.forms.delete', [
                                        'charge' => $charge,
                                    ])
                                    @if ($signatureContent)
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm btn-signature-view"
                                            title="Ver firma" data-signature='@json($signatureContent)'
                                            data-signer="{{ $charge->signature?->signer?->name ?? '' }}"
                                            data-titularidad="{{ $charge->signature?->titularidad ? '1' : '0' }}"
                                            data-parentesco="{{ $charge->signature?->parentesco ?? '' }}"
                                            data-titular-name="{{ $charge->resolucion?->nombres_apellidos ?? '' }}"
                                            data-titular-dni="{{ $charge->resolucion?->dni ?? '' }}"
                                            data-evidence='@json($evidenceData)'>
                                            <i class="fa-solid fa-eye"></i> Ver firma
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fa-solid fa-inbox me-2"></i>No hay cargos recibidos.
                        </div>
                    @endforelse
                </div>
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>No. de cargo</th>
                                <th>Periodo</th>
                                <th>Enviado por</th>
                                <th>Interesado</th>
                                <th>Asunto</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($receivedCharges as $key => $charge)
                                @php
                                    $signatureContent = null;
                                    if (
                                        $charge->signature?->signature_root &&
                                        \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                            $charge->signature->signature_root,
                                        )
                                    ) {
                                        $signatureContent = \Illuminate\Support\Facades\Storage::disk('local')->get(
                                            $charge->signature->signature_root,
                                        );
                                    }
                                    $cartaPoderData = null;
                                    if (
                                        $charge->signature?->carta_poder_path &&
                                        \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                            $charge->signature->carta_poder_path,
                                        )
                                    ) {
                                        $cartaPath = $charge->signature->carta_poder_path;
                                        $extension = strtolower(pathinfo($cartaPath, PATHINFO_EXTENSION));
                                        $mimeType = match ($extension) {
                                            'jpg', 'jpeg' => 'image/jpeg',
                                            'png' => 'image/png',
                                            'pdf' => 'application/pdf',
                                            default => 'application/octet-stream',
                                        };
                                        $content = \Illuminate\Support\Facades\Storage::disk('local')->get($cartaPath);
                                        $cartaPoderData = 'data:' . $mimeType . ';base64,' . base64_encode($content);
                                    }
                                    $evidenceData = null;
                                    if (
                                        $charge->signature?->evidence_root &&
                                        \Illuminate\Support\Facades\Storage::disk('local')->exists(
                                            $charge->signature->evidence_root,
                                        )
                                    ) {
                                        $evidencePath = $charge->signature->evidence_root;
                                        $extension = strtolower(pathinfo($evidencePath, PATHINFO_EXTENSION));
                                        $mimeType = match ($extension) {
                                            'jpg', 'jpeg' => 'image/jpeg',
                                            'png' => 'image/png',
                                            default => 'application/octet-stream',
                                        };
                                        $content = \Illuminate\Support\Facades\Storage::disk('local')->get(
                                            $evidencePath,
                                        );
                                        $evidenceData = 'data:' . $mimeType . ';base64,' . base64_encode($content);
                                    }
                                    $interesadoLabel =
                                        $charge->tipo_interesado === 'Persona Juridica'
                                            ? ($charge->legalEntity?->razon_social ?:
                                            $charge->legalEntity?->ruc ?:
                                            '???')
                                            : (trim(
                                                ($charge->naturalPerson?->nombres ?? '') .
                                                    ' ' .
                                                    ($charge->naturalPerson?->apellidos ?? ''),
                                            ) ?:
                                            ($charge->naturalPerson?->dni ?:
                                            '???'));
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $key + 1 }}</td>
                                    <td>{{ $charge->n_charge }}</td>
                                    <td>{{ $charge->charge_period ?? '?' }}</td>
                                    <td>{{ $charge->user->name ?? 'â€”' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $interesadoLabel }}</div>
                                        <div class="small text-muted">{{ $charge->tipo_interesado }}
                                        </div>
                                    </td>
                                    <td>{{ $charge->asunto }}</td>
                                    <td>
                                        @if ($charge->signature?->signature_status === 'firmado')
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-circle-check me-1"></i>Firmado
                                            </span>
                                        @elseif ($charge->signature?->signature_status === 'rechazado')
                                            <span class="badge bg-danger">
                                                <i class="fa-solid fa-circle-xmark me-1"></i>Rechazado
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fa-solid fa-hourglass-half me-1"></i>Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-outline-success btn-sm btn-sign-charge" title="Firmar"
                                                data-action="{{ route('charges.sign.store', $charge) }}"
                                                data-charge='@json($charge)'
                                                data-signature='@json($signatureContent ?? '')'
                                                data-signer="{{ $charge->signature?->signer?->name ?? '' }}"
                                                @disabled($charge->signature?->signature_status !== 'pendiente')>
                                                <i class="fa-solid fa-signature"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm btn-reject-charge"
                                                title="Rechazar" data-action="{{ route('charges.reject', $charge) }}"
                                                data-charge-id="{{ $charge->id }}" @disabled($charge->signature?->signature_status !== 'pendiente')>
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                            @include('charges.forms.delete', [
                                                'charge' => $charge,
                                            ])
                                        </div>
                                        @if ($signatureContent)
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm btn-signature-view mt-1"
                                                title="Ver firma" data-signature='@json($signatureContent)'
                                                data-signer="{{ $charge->signature?->signer?->name ?? '' }}"
                                                data-titularidad="{{ $charge->signature?->titularidad ? '1' : '0' }}"
                                                data-parentesco="{{ $charge->signature?->parentesco ?? '' }}"
                                                data-titular-name="{{ $charge->resolucion?->nombres_apellidos ?? '' }}"
                                                data-titular-dni="{{ $charge->resolucion?->dni ?? '' }}"
                                                data-evidence='@json($evidenceData)'>
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-inbox me-2"></i>No hay cargos recibidos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
