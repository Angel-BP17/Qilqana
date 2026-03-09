{{-- Modal rechazar cargo --}}
<div class="modal fade" id="rejectChargeModal" tabindex="-1" aria-labelledby="rejectChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectChargeModalLabel">Rechazar cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="rejectChargeForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="signature_comment" class="form-label">Motivo del rechazo</label>
                        <textarea class="form-control" id="signature_comment" name="signature_comment" rows="3" required></textarea>
                        <div class="form-text">El cargo se ocultara de la bandeja de recibidos, pero quedara para auditoria.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar cargo</button>
                </div>
            </form>
        </div>
    </div>
</div>
