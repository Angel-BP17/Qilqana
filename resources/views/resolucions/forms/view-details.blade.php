<div class="modal fade" id="viewResolucionDetailsModal" tabindex="-1" aria-labelledby="viewResolucionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="viewResolucionDetailsModalLabel">
                    <span class="material-symbols-outlined me-2">description</span>
                    Detalles de Resolución <span id="view_res_rd" class="ms-2 badge bg-white text-primary"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-4">
                {{-- Información de la Resolución --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary">Información de la Resolución</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="text-muted small text-uppercase fw-bold mb-1">Interesado</p>
                                <span id="view_res_interesado" class="fw-semibold text-dark"></span>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small text-uppercase fw-bold mb-1">DNI</p>
                                <span id="view_res_dni" class="text-dark"></span>
                            </div>
                            <div class="col-md-12">
                                <p class="text-muted small text-uppercase fw-bold mb-1">Asunto</p>
                                <span id="view_res_asunto" class="text-dark"></span>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted small text-uppercase fw-bold mb-1">Fecha</p>
                                <span id="view_res_fecha" class="text-dark"></span>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted small text-uppercase fw-bold mb-1">Periodo</p>
                                <span id="view_res_periodo" class="text-dark"></span>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted small text-uppercase fw-bold mb-1">Procedencia</p>
                                <span id="view_res_procedencia" class="text-dark"></span>
                            </div>
                            <div class="col-md-12 d-none" id="view_res_document_wrapper">
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <p class="text-muted small text-uppercase fw-bold mb-0">Documento de la Resolución</p>
                                    <a id="view_res_pdf_link" href="#" target="_blank" class="btn btn-outline-danger btn-sm fw-bold d-inline-flex align-items-center">
                                        <span class="material-symbols-outlined fs-5 me-1">open_in_new</span> Abrir en pestaña nueva
                                    </a>
                                </div>
                                <div class="border rounded overflow-hidden shadow-sm bg-white" style="height: 550px;">
                                    <iframe id="view_res_pdf_iframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4" id="view_res_charge_info" style="display:none;">
                    <div class="col-12">
                        <hr class="my-0">
                        <h6 class="mt-4 mb-3 fw-bold text-secondary d-flex align-items-center">
                            <span class="material-symbols-outlined me-2">receipt_long</span>
                            Información del Cargo Asociado
                        </h6>
                    </div>

                    {{-- Documento del Cargo --}}
                    <div class="col-12" id="view_res_document_section" style="display:none;">
                        <div class="card border-0 shadow-sm border-start border-primary border-4">
                            <div class="card-body d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <span class="material-symbols-outlined text-primary fs-1 me-3">picture_as_pdf</span>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Documento Adjunto</h6>
                                        <small class="text-muted">Archivo PDF del cargo registrado</small>
                                    </div>
                                </div>
                                <a id="view_res_document_link" href="#" target="_blank" class="btn btn-primary d-flex align-items-center">
                                    <span class="material-symbols-outlined me-1">download</span> Ver / Descargar
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Firma --}}
                    <div class="col-12" id="view_res_signature_section" style="display:none;">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold text-success">Firma Digital</h6>
                            </div>
                            <div class="card-body">
                                <div id="view_res_signer_info" class="alert alert-success border-0 mb-3 small py-2"></div>
                                <div id="view_res_signature_content" class="text-center border rounded p-3 bg-white"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Evidencia --}}
                    <div class="col-md-6" id="view_res_evidence_section" style="display:none;">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-warning">Evidencia Física</h6>
                                <a id="view_res_map_link" href="#" target="_blank" class="btn btn-sm btn-outline-warning p-1 py-0 d-none" title="Ver ubicación en el mapa">
                                    <span class="material-symbols-outlined fs-6">location_on</span>
                                </a>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="view_res_evidence_content" class="rounded overflow-hidden border"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Carta Poder --}}
                    <div class="col-md-6" id="view_res_carta_poder_section" style="display:none;">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-info">Carta Poder</h6>
                                <a id="view_res_carta_poder_link" href="#" target="_blank" class="btn btn-sm btn-outline-info p-1 py-0">
                                    <span class="material-symbols-outlined fs-6">open_in_new</span>
                                </a>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="view_res_carta_poder_content" class="rounded overflow-hidden border"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view_res_no_charge_alert" class="alert alert-light border text-center p-4" style="display:none;">
                    <span class="material-symbols-outlined fs-1 text-muted d-block mb-2">pending_actions</span>
                    <p class="mb-0 text-muted">Esta resolución aún no tiene un cargo asociado o firmado.</p>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
