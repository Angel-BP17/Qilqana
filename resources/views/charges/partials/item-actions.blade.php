<div class="btn-group">
    @if(isset($canEdit) && $canEdit)
        <button type="button" class="btn btn-outline-primary btn-sm btn-edit-charge"
            title="Editar" data-action="{{ route('charges.update', $charge) }}"
            data-charge='@json($charge)'
            data-assigned="{{ $charge->signature?->assigned_to }}"
            @disabled($charge->signature?->signature_status !== 'pendiente')>
            <i class="fa-solid fa-pen"></i>
        </button>
    @endif

    @if(isset($canSign) && $canSign)
        <button type="button" class="btn btn-outline-success btn-sm btn-sign-charge"
            title="Firmar" data-action="{{ route('charges.sign.store', $charge) }}"
            data-charge='@json($charge)'
            @disabled($charge->signature?->signature_status !== 'pendiente')>
            <i class="fa-solid fa-signature"></i>
        </button>
    @endif

    @if(isset($canReject) && $canReject)
        <button type="button" class="btn btn-outline-danger btn-sm btn-reject-charge"
            title="Rechazar" data-action="{{ route('charges.reject', $charge) }}"
            data-charge-id="{{ $charge->id }}" @disabled($charge->signature?->signature_status !== 'pendiente')>
            <i class="fa-solid fa-ban"></i>
        </button>
    @endif

    @include('charges.forms.delete', [
        'charge' => $charge,
        'disabled' => ($charge->signature?->signature_status !== 'pendiente' && !Auth::user()->hasRole('ADMINISTRADOR')),
    ])
</div>

<div class="mt-1">
    @if ($charge->has_signature)
        <button type="button" class="btn btn-outline-secondary btn-sm btn-signature-view"
            title="Ver firma" 
            data-url="{{ route('charges.file.signature', $charge) }}"
            data-signer="{{ $charge->signature?->signer?->name ?? '' }}"
            data-titularidad="{{ $charge->signature?->titularidad ? '1' : '0' }}"
            data-parentesco="{{ $charge->signature?->parentesco ?? '' }}"
            data-titular-name="{{ $charge->resolucion?->nombres_apellidos ?? '' }}"
            data-titular-dni="{{ $charge->resolucion?->dni ?? '' }}"
            data-evidence="{{ $charge->has_evidence ? route('charges.file.evidence', $charge) : '' }}">
            <i class="fa-solid fa-eye"></i>
        </button>
    @endif

    @if ($charge->has_carta_poder)
        <button type="button" class="btn btn-outline-info btn-sm btn-carta-poder-view"
            title="Ver carta poder" data-url="{{ route('charges.file.carta-poder', $charge) }}">
            <i class="fa-solid fa-file-image"></i>
        </button>
    @endif
</div>
