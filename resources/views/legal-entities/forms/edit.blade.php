<div class="modal fade" id="editLegalEntityModal" tabindex="-1" aria-labelledby="editLegalEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold" id="editLegalEntityModalLabel">
                    <span class="material-symbols-outlined me-2">edit_square</span>Editar persona jurídica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editLegalEntityForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="edit_ruc" class="form-label fw-semibold">RUC</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <span class="material-symbols-outlined">featured_play_list</span>
                                </span>
                                <input type="text" class="form-control input-lookup-special border-start-0" id="edit_ruc" name="ruc"
                                    inputmode="numeric" minlength="11" maxlength="11" pattern="\d{11}" placeholder="RUC">
                                <button class="btn btn-lookup-special px-4" type="button" id="lookup_ruc_btn_edit">
                                    <span class="material-symbols-outlined me-1">search</span> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="edit_razon_social" class="form-label fw-semibold">Razón social</label>
                            <input type="text" class="form-control" id="edit_razon_social" name="razon_social"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="RAZÓN SOCIAL">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="edit_district" class="form-label fw-semibold">Distrito</label>
                            <input type="text" class="form-control" id="edit_district" name="district"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="DISTRITO" required>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="border rounded-3 p-3 bg-light shadow-sm">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-primary fs-5">badge</span>
                                    <h6 class="mb-0 fw-bold text-primary text-uppercase small">Datos del Representante</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_dni" class="form-label fw-semibold">DNI representante</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-lookup-special" id="edit_representative_dni" name="representative_dni" maxlength="10" placeholder="DNI">
                                            <button class="btn btn-lookup-special" type="button" id="lookup_representative_dni_btn_entities_edit">
                                                <span class="material-symbols-outlined">search</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_nombres" class="form-label fw-semibold">Nombres</label>
                                        <input type="text" class="form-control" id="edit_representative_nombres" name="representative_nombres"
                                            style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="NOMBRES">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_apellido_paterno" class="form-label fw-semibold">Apellido paterno</label>
                                        <input type="text" class="form-control" id="edit_representative_apellido_paterno" name="representative_apellido_paterno"
                                            style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO PATERNO">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_apellido_materno" class="form-label fw-semibold">Apellido materno</label>
                                        <input type="text" class="form-control" id="edit_representative_apellido_materno" name="representative_apellido_materno"
                                            style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO MATERNO">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_cargo" class="form-label fw-semibold">Cargo</label>
                                        <input type="text" class="form-control" id="edit_representative_cargo" name="representative_cargo"
                                            style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="CARGO">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_since" class="form-label fw-semibold">Representante desde</label>
                                        <input type="date" class="form-control" id="edit_representative_since" name="representative_since">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <span class="material-symbols-outlined me-1">check_circle</span> Actualizar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
