<div class="modal fade" id="createLegalEntityModal" tabindex="-1" aria-labelledby="createLegalEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="createLegalEntityModalLabel">Registrar persona juridica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('legal-entities.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="ruc" class="form-label">RUC</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ruc" name="ruc" value="{{ old('ruc') }}"
                                    inputmode="numeric" minlength="11" maxlength="11" pattern="\d{11}" placeholder="11 digitos">
                                <button class="btn btn-outline-primary" type="button" id="lookup_ruc_btn">Buscar</button>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="razon_social" class="form-label">Razon social</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social"
                                value="{{ old('razon_social') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" placeholder="Razon social">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="district" class="form-label">Distrito</label>
                            <input type="text" class="form-control" id="district" name="district"
                                value="{{ old('district') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" placeholder="Distrito" required>
                        </div>
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fa-solid fa-id-card text-primary"></i>
                                    <h6 class="mb-0 fw-semibold">Representante</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label for="representative_dni" class="form-label">DNI representante</label>
                                        <input type="text" class="form-control" id="representative_dni" name="representative_dni"
                                            value="{{ old('representative_dni') }}" inputmode="numeric" maxlength="10"
                                            placeholder="Documento">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label for="representative_name" class="form-label">Nombre representante</label>
                                        <input type="text" class="form-control" id="representative_name" name="representative_name"
                                            value="{{ old('representative_name') }}" style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase();" placeholder="Nombres y apellidos">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="representative_cargo" class="form-label">Cargo representante</label>
                                        <input type="text" class="form-control" id="representative_cargo" name="representative_cargo"
                                            value="{{ old('representative_cargo') }}" style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase();" placeholder="Cargo">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="representative_since" class="form-label">Representante desde</label>
                                        <input type="date" class="form-control" id="representative_since" name="representative_since"
                                            value="{{ old('representative_since') }}">
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
