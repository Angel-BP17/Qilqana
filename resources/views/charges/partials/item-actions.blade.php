<div class="d-flex gap-1 align-items-center">
    <div class="btn-group" role="group" aria-label="Acciones de gestión de cargo">
        @if(isset($canEdit) && $canEdit)
            <button type="button" class="btn btn-outline-primary btn-sm btn-edit-charge"
                title="Editar" aria-label="Editar información del cargo"
                data-action="{{ route('charges.update', $charge) }}"
                data-charge='@json($charge)'
                data-assigned="{{ $charge->signature?->assigned_to }}"
                @disabled($charge->signature?->signature_status !== 'pendiente')>
                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
            </button>
        @endif

        @if(isset($canSign) && $canSign)
            <button type="button" class="btn btn-outline-success btn-sm btn-sign-charge"
                title="Firmar" aria-label="Firmar documento del cargo"
                data-action="{{ route('charges.sign.store', $charge) }}"
                data-charge='@json($charge)'
                @disabled($charge->signature?->signature_status !== 'pendiente')>
                <span class="material-symbols-outlined" aria-hidden="true">history_edu</span>
            </button>
        @endif

        @if(isset($canReject) && $canReject)
            <button type="button" class="btn btn-outline-danger btn-sm btn-reject-charge"
                title="Rechazar" aria-label="Rechazar cargo con comentario"
                data-action="{{ route('charges.reject', $charge) }}"
                data-charge-id="{{ $charge->id }}" @disabled($charge->signature?->signature_status !== 'pendiente')>
                <span class="material-symbols-outlined" aria-hidden="true">block</span>
            </button>
        @endif

        @include('charges.forms.delete', [
            'charge' => $charge,
            'disabled' => ($charge->signature?->signature_status !== 'pendiente' && !Auth::user()->hasRole('ADMINISTRADOR')),
        ])
    </div>

    <button type="button" class="btn btn-outline-secondary btn-sm btn-view-charge-details"
        title="Ver detalles" aria-label="Ver todos los detalles del cargo #{{ $charge->n_charge }}"
        data-charge-id="{{ $charge->id }}"
        data-n-charge="{{ $charge->n_charge }}"
        data-interesado="{{ $charge->interesado_label }}"
        data-tipo="{{ $charge->tipo_interesado }}"
        data-asunto="{{ $charge->asunto }}"
        data-fecha="{{ $charge->document_date ? \Carbon\Carbon::parse($charge->document_date)->format('d/m/Y') : '---' }}"
        data-user="{{ $charge->user?->name }} {{ $charge->user?->last_name }}"
        data-status="{{ ucfirst($charge->signature?->signature_status ?? 'pendiente') }}"
        data-has-signature="{{ $charge->has_signature ? '1' : '0' }}"
        data-signature-url="{{ $charge->file_signature_url }}"
        data-signer="{{ $charge->signature?->signer?->name ?? '' }} {{ $charge->signature?->signer?->last_name ?? '' }}"
        data-titularidad="{{ $charge->signature?->titularidad ? '1' : '0' }}"
        data-parentesco="{{ $charge->signature?->parentesco ?? '' }}"
        data-titular-name="{{ $charge->resolucion?->nombres_apellidos ?? $charge->naturalPerson?->full_name ?? '' }}"
        data-has-document="{{ $charge->document_path ? '1' : '0' }}"
        data-document-url="{{ $charge->file_document_url }}"
        data-has-evidence="{{ $charge->has_evidence ? '1' : '0' }}"
        data-evidence-url="{{ $charge->file_evidence_url }}"
        data-evidence-location='@json($charge->signature?->evidence_location)'
        data-has-carta-poder="{{ $charge->has_carta_poder ? '1' : '0' }}"
        data-carta-poder-url="{{ $charge->file_carta_poder_url }}">
        <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
    </button>
</div>
