{{-- Modal info usuario --}}
<div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="userInfoModalLabel">Detalles del usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Nombres</p>
                        <p class="fw-semibold" id="info_name">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Apellidos</p>
                        <p class="fw-semibold" id="info_last_name">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">DNI</p>
                        <p class="fw-semibold" id="info_dni">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Roles</p>
                        <p class="fw-semibold" id="info_user_type">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Creado</p>
                        <p class="fw-semibold" id="info_created_at">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Actualizado</p>
                        <p class="fw-semibold" id="info_updated_at">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
