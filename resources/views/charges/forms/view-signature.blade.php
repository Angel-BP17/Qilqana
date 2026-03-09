{{-- Modal ver firma --}}
<div class="modal fade" id="viewSignatureModal" tabindex="-1" aria-labelledby="viewSignatureModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="viewSignatureModalLabel">Firma del cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 text-muted" id="viewSignatureSigner" style="display:none;"></div>
                <div class="mb-3" id="viewSignatureExtra" style="display:none;"></div>
                <div id="viewSignatureContent" class="border rounded p-3 bg-white text-center"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
