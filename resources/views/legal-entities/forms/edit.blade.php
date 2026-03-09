<div class="modal fade" id="editLegalEntityModal" tabindex="-1" aria-labelledby="editLegalEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editLegalEntityModalLabel">Editar persona juridica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editLegalEntityForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="edit_ruc" class="form-label">RUC</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="edit_ruc" name="ruc"
                                    inputmode="numeric" minlength="11" maxlength="11" pattern="\d{11}">
                                <button class="btn btn-outline-primary" type="button" id="lookup_ruc_btn_edit">Buscar</button>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="edit_razon_social" class="form-label">Razon social</label>
                            <input type="text" class="form-control" id="edit_razon_social" name="razon_social"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="edit_district" class="form-label">Distrito</label>
                            <input type="text" class="form-control" id="edit_district" name="district"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" required>
                        </div>
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fa-solid fa-id-card text-primary"></i>
                                    <h6 class="mb-0 fw-semibold">Representante</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label for="edit_representative_dni" class="form-label">DNI representante</label>
                                        <input type="text" class="form-control" id="edit_representative_dni" name="representative_dni"
                                            inputmode="numeric" maxlength="10">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label for="edit_representative_name" class="form-label">Nombre representante</label>
                                        <input type="text" class="form-control" id="edit_representative_name" name="representative_name"
                                            style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_cargo" class="form-label">Cargo representante</label>
                                        <input type="text" class="form-control" id="edit_representative_cargo" name="representative_cargo"
                                            style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="edit_representative_since" class="form-label">Representante desde</label>
                                        <input type="date" class="form-control" id="edit_representative_since" name="representative_since">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
