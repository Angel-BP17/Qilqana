<div class="d-flex gap-1 align-items-center">
    <div class="btn-group">
        @if(isset($canEdit) && $canEdit)
            <button type="button" class="btn btn-outline-primary btn-sm btn-edit-charge"
                title="Editar" data-action="{{ route('charges.update', $charge) }}"
                data-charge='@json($charge)'
                data-assigned="{{ $charge->signature?->assigned_to }}"
                @disabled($charge->signature?->signature_status !== 'pendiente')>
                <span class="material-symbols-outlined">edit</span>
            </button>
        @endif

        @if(isset($canSign) && $canSign)
            <button type="button" class="btn btn-outline-success btn-sm btn-sign-charge"
                title="Firmar" data-action="{{ route('charges.sign.store', $charge) }}"
                data-charge='@json($charge)'
                @disabled($charge->signature?->signature_status !== 'pendiente')>
                <span class="material-symbols-outlined">history_edu</span>
            </button>
        @endif

        @if(isset($canReject) && $canReject)
            <button type="button" class="btn btn-outline-danger btn-sm btn-reject-charge"
                title="Rechazar" data-action="{{ route('charges.reject', $charge) }}"
                data-charge-id="{{ $charge->id }}" @disabled($charge->signature?->signature_status !== 'pendiente')>
                <span class="material-symbols-outlined">block</span>
            </button>
        @endif

        @include('charges.forms.delete', [
            'charge' => $charge,
            'disabled' => ($charge->signature?->signature_status !== 'pendiente' && !Auth::user()->hasRole('ADMINISTRADOR')),
        ])
    </div>

    <button type="button" class="btn btn-outline-secondary btn-sm btn-view-charge-details"
        title="Ver detalles" 
        data-charge-id="{{ $charge->id }}"
        data-n-charge="{{ $charge->n_charge }}"
        data-interesado="{{ $charge->interesado_label }}"
        data-tipo="{{ $charge->tipo_interesado }}"
        data-asunto="{{ $charge->asunto }}"
        data-fecha="{{ $charge->document_date ? \Carbon\Carbon::parse($charge->document_date)->format('d/m/Y') : '---' }}"
        data-user="{{ $charge->user?->name }} {{ $charge->user?->last_name }}"
        data-status="{{ ucfirst($charge->signature?->signature_status ?? 'pendiente') }}"
        data-has-signature="{{ $charge->has_signature ? '1' : '0' }}"
        data-signature-url="{{ route('charges.file.signature', $charge) }}"
        data-signer="{{ $charge->signature?->signer?->name ?? '' }} {{ $charge->signature?->signer?->last_name ?? '' }}"
        data-titularidad="{{ $charge->signature?->titularidad ? '1' : '0' }}"
        data-parentesco="{{ $charge->signature?->parentesco ?? '' }}"
        data-titular-name="{{ $charge->resolucion?->nombres_apellidos ?? $charge->naturalPerson?->full_name ?? '' }}"
        data-has-evidence="{{ $charge->has_evidence ? '1' : '0' }}"
        data-evidence-url="{{ route('charges.file.evidence', $charge) }}"
        data-has-carta-poder="{{ $charge->has_carta_poder ? '1' : '0' }}"
        data-carta-poder-url="{{ route('charges.file.carta-poder', $charge) }}">
        <span class="material-symbols-outlined">visibility</span>
    </button>
</div>
