<div class="modal fade" id="signChargeModal" tabindex="-1" aria-labelledby="signChargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="signChargeModalLabel">Firmar cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="signChargeForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 d-flex gap-3 align-items-center flex-wrap">
                        <span id="sign_firmado_state" class="badge"></span>
                        <span class="text-muted" id="sign_signed_by" style="display:none;">
                            Firmado por: <strong id="sign_signed_by_name"></strong>
                        </span>
                    </div>
                    <div class="border rounded border-primary-subtle p-3 bg-white mb-3 d-none"
                        id="sign_external_fields">
                        <h6 class="mb-3">Datos de titularidad</h6>
                        <div class="mb-3">
                            <p class="mb-2">Titularidad</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="sign_titularidad_yes"
                                    name="titularidad" value="1" checked>
                                <label class="form-check-label" for="sign_titularidad_yes">
                                    Soy titular
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="sign_titularidad_no"
                                    name="titularidad" value="0">
                                <label class="form-check-label" for="sign_titularidad_no">
                                    No soy titular
                                </label>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 d-none" id="sign_parentesco_group">
                                <label for="sign_parentesco" class="form-label">Parentesco</label>
                                <input type="text" class="form-control" id="sign_parentesco" name="parentesco">
                            </div>
                            <div class="col-md-6 d-none" id="sign_carta_poder_group">
                                <label for="sign_carta_poder" class="form-label">Carta poder</label>
                                <input type="file" class="form-control" id="sign_carta_poder" name="carta_poder"
                                    accept=".jpg,.jpeg,.png,.pdf">
                                <div class="form-text">Adjunta la carta poder en formato PDF o imagen.</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <p class="mb-0 fw-semibold">Panel de firma</p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-danger" id="clear-signature" type="button">
                                    <span class="material-symbols-outlined">ink_eraser</span> Limpiar
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="undo" type="button">
                                    <span class="material-symbols-outlined">undo</span> Deshacer
                                </button>
                            </div>
                        </div>
                        <div class="signature-pad-container border border-primary-subtle rounded p-2 bg-light">
                            <canvas id="signature-pad" class="signature-pad w-100" style="height: 240px;"></canvas>
                        </div>
                        <div class="form-text mt-2">
                            Dibuje la firma y confirme para marcar el cargo como firmado.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="sign_evidence_root" class="form-label">Evidencia (opcional)</label>
                        <input type="file" class="form-control" id="sign_evidence_root" name="evidence_root"
                            accept=".jpg,.jpeg,.png">
                        <div class="form-text">Adjunte una foto de la evidencia (JPG o PNG).</div>
                    </div>
                    <div class="signature-preview" id="signaturePreviewContainer" style="display:none;">
                        <p class="mb-2 text-muted">Vista previa de la firma:</p>
                        <div id="signaturePreview" class="p-2 border rounded bg-white"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-3">
                        <span class="material-symbols-outlined">info</span> Asegurese de que la firma sea clara y legible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="confirm-signature" disabled>
                        <span class="material-symbols-outlined me-1">edit</span> Confirmar firma y guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
