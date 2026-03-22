<div class="modal fade" id="editNaturalPersonModal" tabindex="-1" aria-labelledby="editNaturalPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold" id="editNaturalPersonModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Editar persona natural
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editNaturalPersonForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="edit_dni" class="form-label fw-semibold">DNI</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-card-text"></i>
                                </span>
                                <input type="text" class="form-control input-lookup-special border-start-0" id="edit_dni" name="dni"
                                    minlength="8" maxlength="10" inputmode="numeric" pattern="\d{8,10}" placeholder="DNI">
                                <button class="btn btn-lookup-special px-4" type="button" id="lookup_dni_btn_edit">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="text-muted opacity-25">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="edit_nombres" class="form-label fw-semibold">Nombres</label>
                            <input type="text" class="form-control" id="edit_nombres" name="nombres"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="NOMBRES">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="edit_apellido_paterno" class="form-label fw-semibold">Apellido paterno</label>
                            <input type="text" class="form-control" id="edit_apellido_paterno" name="apellido_paterno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO PATERNO">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="edit_apellido_materno" class="form-label fw-semibold">Apellido materno</label>
                            <input type="text" class="form-control" id="edit_apellido_materno" name="apellido_materno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO MATERNO">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Actualizar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
