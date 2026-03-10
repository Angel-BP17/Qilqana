    <div class="tab-pane fade {{ $active ?? false ? 'show active' : '' }}" id="resolution-tab-pane" role="tabpanel"
        aria-labelledby="resolution-tab" tabindex="0">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info border-0 py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-white">Cargos firmados de resoluciones</h5>
                        @php
                            $resolutionSearch = request('resolution_search');

                            if ($resolutionSearch) {
                                $signedResolutionCharges = $signedResolutionCharges->filter(function ($charge) use (
                                    $resolutionSearch,
                                ) {
                                    $resolucion = $charge->resolucion;
                                    $fecha = $resolucion?->fecha
                                        ? \Carbon\Carbon::parse($resolucion->fecha)->format('Y-m-d')
                                        : '';
                                    $periodo = $resolucion?->periodo ?? '';
                                    $haystack = trim(
                                        ($charge->n_charge ?? '') .
                                            ' ' .
                                            ($resolucion?->rd ?? '') .
                                            ' ' .
                                            ($resolucion?->nombres_apellidos ?? '') .
                                            ' ' .
                                            ($resolucion?->dni ?? '') .
                                            ' ' .
                                            ($resolucion?->asunto ?? '') .
                                            ' ' .
                                            $fecha .
                                            ' ' .
                                            $periodo,
                                    );
                                    return $haystack !== '' && stripos($haystack, $resolutionSearch) !== false;
                                });
                            }
                        @endphp
                        <span class="badge bg-light text-dark">{{ $signedResolutionCharges->count() }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="d-md-none d-flex gap-2">
                            <button class="btn btn-light" type="button" data-bs-toggle="collapse"
                                data-bs-target="#resolution-filters">
                                <i class="fa-solid fa-filter me-1"></i> Filtros
                            </button>
                            <button class="btn btn-light" type="button" data-bs-toggle="collapse"
                                data-bs-target="#resolution-pdf">
                                <i class="fa-solid fa-file-pdf me-1"></i> PDF
                            </button>
                        </div>
                        <div class="d-none d-md-flex gap-2 align-items-center">
                            <form class="d-flex flex-wrap gap-2 row" action="{{ route('charges.index') }}"
                                method="GET">
                                @foreach (request()->except('resolution_search', 'resolution_period') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <div class="col">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fa-solid fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0"
                                            name="resolution_search"
                                            placeholder="No. cargo, RD, nombres/apellidos, DNI, fecha o periodo"
                                            value="{{ $resolutionSearch }}">
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
                                    <button class="btn btn-light" type="submit"><i class="fas fa-filter"></i>
                                        Filtrar</button>
                                </div>
                            </form>
                            <form action="{{ route('charges.reports.resolution') }}" method="GET">
                                @if ($resolutionSearch)
                                    <input type="hidden" name="resolution_search" value="{{ $resolutionSearch }}">
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
            <div class="collapse d-md-none px-3 pt-3 pb-3" id="resolution-filters">
                <form class="d-flex flex-wrap gap-2 row" action="{{ route('charges.index') }}" method="GET">
                    @foreach (request()->except('resolution_search', 'resolution_period') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" name="resolution_search"
                                placeholder="No. cargo, RD, nombres/apellidos, DNI, fecha o periodo"
                                value="{{ $resolutionSearch }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <select name="resolution_period" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos los periodos</option>
                            @foreach ($periodOptions ?? [] as $period)
                                <option value="{{ $period }}" @selected(($resolutionPeriod ?? request('resolution_period')) === $period)>
                                    {{ $period }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
            <div class="collapse d-md-none px-3 pt-3 pb-3" id="resolution-pdf">
                <form action="{{ route('charges.reports.resolution') }}" method="GET">
                    @if ($resolutionSearch)
                        <input type="hidden" name="resolution_search" value="{{ $resolutionSearch }}">
                    @endif
                    @if ($resolutionPeriod)
                        <input type="hidden" name="resolution_period" value="{{ $resolutionPeriod }}">
                    @endif
                    <button class="btn btn-light w-100" type="submit">
                        <i class="fa-solid fa-file-pdf me-1"></i> Reporte PDF
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="d-md-none p-3">
                    @forelse ($signedResolutionCharges as $key => $charge)
                        @php
                            $signatureStatus = $charge->signature?->signature_status;
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
                                        @if ($signatureStatus === 'firmado')
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-circle-check me-1"></i>Firmado
                                            </span>
                                        @elseif ($signatureStatus === 'rechazado')
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
                                <div class="mt-2">
                                    <div class="small text-muted text-uppercase">RD</div>
                                    <div class="fw-semibold">{{ $charge->resolucion?->rd ?? 'â€”' }}</div>
                                </div>
                                <div class="mt-2">
                                    <div class="small text-muted text-uppercase">Fecha</div>
                                    <div class="fw-semibold">
                                        {{ $charge->resolucion?->fecha ? \Carbon\Carbon::parse($charge->resolucion->fecha)->format('Y-m-d') : 'â€”' }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="small text-muted text-uppercase">Nombres y apellidos</div>
                                    <div class="fw-semibold">
                                        {{ $charge->resolucion?->nombres_apellidos ?? 'â€”' }}</div>
                                </div>
                                <div class="mt-2">
                                    <div class="small text-muted text-uppercase">DNI</div>
                                    <div class="fw-semibold">{{ $charge->resolucion?->dni ?? 'â€”' }}</div>
                                </div>
                                <div class="mt-2">
                                    <div class="small text-muted text-uppercase">Asunto</div>
                                    <div class="fw-semibold">{{ $charge->resolucion?->asunto ?? 'â€”' }}</div>
                                </div>
                                <div class="mt-3 d-flex flex-wrap gap-2">
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
                                    @if ($cartaPoderData)
                                        <button type="button"
                                            class="btn btn-outline-info btn-sm btn-carta-poder-view"
                                            title="Ver carta poder" data-carta='@json($cartaPoderData)'>
                                            <i class="fa-solid fa-file-image"></i> Ver carta poder
                                        </button>
                                    @endif
                                    @include('charges.forms.delete', [
                                        'charge' => $charge,
                                    ])
                                    @if (!$signatureContent && !$cartaPoderData)
                                        <span class="text-muted">Sin archivos</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fa-solid fa-inbox me-2"></i>No hay cargos de resoluciones.
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
                                <th>RD</th>
                                <th>Fecha</th>
                                <th>Nombres y apellidos</th>
                                <th>DNI</th>
                                <th>Asunto</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($signedResolutionCharges as $key => $charge)
                                @php
                                    $signatureStatus = $charge->signature?->signature_status;
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
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $key + 1 }}</td>
                                    <td>{{ $charge->n_charge }}</td>
                                    <td>{{ $charge->charge_period ?? '?' }}</td>
                                    <td>{{ $charge->resolucion?->rd ?? '—' }}</td>
                                    <td>
                                        {{ $charge->resolucion?->fecha ? \Carbon\Carbon::parse($charge->resolucion->fecha)->format('Y-m-d') : '—' }}
                                    </td>
                                    <td>{{ $charge->resolucion?->nombres_apellidos ?? '—' }}</td>
                                    <td>{{ $charge->resolucion?->dni ?? '—' }}</td>
                                    <td>{{ $charge->resolucion?->asunto ?? '—' }}</td>
                                    <td>
                                        @if ($signatureStatus === 'firmado')
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-circle-check me-1"></i>Firmado
                                            </span>
                                        @elseif ($signatureStatus === 'rechazado')
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
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            @endif
                                            @if ($cartaPoderData)
                                                <button type="button"
                                                    class="btn btn-outline-info btn-sm btn-carta-poder-view"
                                                    title="Ver carta poder" data-carta='@json($cartaPoderData)'>
                                                    <i class="fa-solid fa-file-image"></i>
                                                </button>
                                            @endif
                                            @include('charges.forms.delete', [
                                                'charge' => $charge,
                                            ])
                                        </div>
                                        @if (!$signatureContent && !$cartaPoderData)
                                            <span class="text-muted">Sin archivos</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-inbox me-2"></i>No hay cargos de resoluciones.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
