<div class="modal fade" id="viewChargeDetailsModal" tabindex="-1" aria-labelledby="viewChargeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="viewChargeDetailsModalLabel">
                    <span class="material-symbols-outlined me-2">info</span>
                    Detalles del Cargo <span id="view_charge_number" class="ms-2 badge bg-white text-primary"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-4">
                {{-- Información del Cargo --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary">Información General</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Interesado</label>
                                <span id="view_charge_interesado" class="fw-semibold"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Tipo</label>
                                <span id="view_charge_tipo" class="badge bg-info-subtle text-info border border-info-subtle"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Asunto</label>
                                <span id="view_charge_asunto"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Fecha del Documento</label>
                                <span id="view_charge_fecha"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Registrado por</label>
                                <span id="view_charge_user"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Estado de Firma</label>
                                <span id="view_charge_status"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Firma --}}
                    <div class="col-12" id="view_charge_signature_section" style="display:none;">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold text-success">Firma Digital</h6>
                            </div>
                            <div class="card-body">
                                <div id="view_charge_signer_info" class="alert alert-success border-0 mb-3 small py-2"></div>
                                <div id="view_charge_signature_content" class="text-center border rounded p-3 bg-white"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Evidencia --}}
                    <div class="col-md-6" id="view_charge_evidence_section" style="display:none;">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold text-warning">Evidencia Física</h6>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="view_charge_evidence_content" class="rounded overflow-hidden border"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Carta Poder --}}
                    <div class="col-md-6" id="view_charge_carta_poder_section" style="display:none;">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-info">Carta Poder</h6>
                                <a id="view_charge_carta_poder_link" href="#" target="_blank" class="btn btn-sm btn-outline-info p-1 py-0">
                                    <span class="material-symbols-outlined fs-6">open_in_new</span>
                                </a>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="view_charge_carta_poder_content" class="rounded overflow-hidden border"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
