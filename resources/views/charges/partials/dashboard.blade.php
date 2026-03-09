@php
    $currentUserId = Auth::id();
    $sentChargesFiltered = $sentCharges->filter(fn($charge) => $charge->signature?->assigned_to !== $currentUserId);
    $sentTotal = $sentChargesFiltered->count();
    $sentPending = $sentChargesFiltered
        ->filter(fn($charge) => $charge->signature?->signature_status === 'pendiente')
        ->count();
    $sentSigned = $sentChargesFiltered
        ->filter(fn($charge) => $charge->signature?->signature_status === 'firmado')
        ->count();
    $receivedTotal = $receivedCharges->count();
    $receivedPending = $receivedCharges
        ->filter(fn($charge) => $charge->signature?->signature_status === 'pendiente')
        ->count();
    $receivedSigned = $receivedCharges
        ->filter(fn($charge) => $charge->signature?->signature_status === 'firmado')
        ->count();
    $receivedRejected = $receivedCharges
        ->filter(fn($charge) => $charge->signature?->signature_status === 'rechazado')
        ->count();
    $createdTotal = $createdCharges->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
                    <i class="fa-solid fa-calendar-days fs-4"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted">Periodo asignado</p>
                    <h5 class="mb-0 fw-bold">{{ $defaultPeriod ?? 'Sin periodo' }}</h5>
                    @if (!$defaultPeriod && !Auth::user()?->hasRole('ADMINISTRADOR'))
                        <small class="text-danger">Notificar al administrador</small>
                    @endif
                    @if (!$defaultPeriod && Auth::user()?->hasRole('ADMINISTRADOR'))
                        <a href="{{ route('settings.index') }}" class="btn btn-sm btn-outline-info mt-2">
                            Configurar periodo
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                    <i class="fa-solid fa-check fs-4"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted">Cargos firmados</p>
                    <h4 class="mb-0 fw-bold">{{ $signedCount ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3">
                    <i class="fa-solid fa-pen-to-square fs-4"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted">Pendientes de firma</p>
                    <h4 class="mb-0 fw-bold">{{ $unsignedCount ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 text-muted">Nuevo cargo</p>
                    <h6 class="mb-0">Registrar rapidamente</h6>
                </div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#createChargeModal" @disabled(!$defaultPeriod)
                    @if (!$defaultPeriod) title="Configura el periodo para crear cargos" @endif>
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
</div>
<ul class="nav nav-tabs mb-3" id="charges-tabs" role="tablist">
    @if ($canViewResolutionCharges)
        <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center gap-2" id="resolution-tab" data-bs-toggle="tab"
                data-bs-target="#resolution-tab-pane" type="button" role="tab" aria-controls="resolution-tab-pane"
                aria-selected="true">
                Resoluciones <span class="badge bg-secondary ms-1">{{ $resolutionCharges->count() }}</span>
            </button>
        </li>
    @endif
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ !$canViewResolutionCharges ? 'active' : '' }} d-flex align-items-center gap-2" id="received-tab" data-bs-toggle="tab"
            data-bs-target="#received-tab-pane" type="button" role="tab" aria-controls="received-tab-pane"
            aria-selected="{{ !$canViewResolutionCharges ? 'true' : 'false' }}">
            Recibidos <span class="badge bg-secondary ms-1">{{ $receivedTotal }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link d-flex align-items-center gap-2" id="created-tab" data-bs-toggle="tab"
            data-bs-target="#created-tab-pane" type="button" role="tab" aria-controls="created-tab-pane"
            aria-selected="false">
            Creados <span class="badge bg-secondary ms-1">{{ $createdTotal }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link d-flex align-items-center gap-2" id="sent-tab" data-bs-toggle="tab"
            data-bs-target="#sent-tab-pane" type="button" role="tab" aria-controls="sent-tab-pane"
            aria-selected="false">
            Enviados <span class="badge bg-secondary ms-1">{{ $sentTotal }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="charges-tabs-content">
    @if ($canViewResolutionCharges)
        @include('charges.partials.lists.resolution', ['active' => true])
    @endif

    @include('charges.partials.lists.received', ['active' => !$canViewResolutionCharges])

    @include('charges.partials.lists.created')

    @include('charges.partials.lists.sent')
</div>
