<div class="modal fade" id="editNaturalPersonModal" tabindex="-1" aria-labelledby="editNaturalPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editNaturalPersonModalLabel">Editar persona natural</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editNaturalPersonForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label for="edit_dni" class="form-label">DNI</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="edit_dni" name="dni" minlength="8" maxlength="10"
                                    inputmode="numeric" pattern="\d{8,10}" placeholder="Documento">
                                <button class="btn btn-outline-primary" type="button" id="lookup_dni_btn_edit">Buscar</button>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="edit_nombres" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="edit_nombres" name="nombres"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="Nombres">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="edit_apellido_paterno" class="form-label">Apellido paterno</label>
                            <input type="text" class="form-control" id="edit_apellido_paterno" name="apellido_paterno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="Apellido paterno">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="edit_apellido_materno" class="form-label">Apellido materno</label>
                            <input type="text" class="form-control" id="edit_apellido_materno" name="apellido_materno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" placeholder="Apellido materno">
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
