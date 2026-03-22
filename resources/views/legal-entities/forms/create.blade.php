<div class="modal fade" id="createLegalEntityModal" tabindex="-1" aria-labelledby="createLegalEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold" id="createLegalEntityModalLabel">
                    <i class="bi bi-building-plus me-2"></i>Registrar persona jurídica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('legal-entities.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="ruc" class="form-label fw-semibold">RUC</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-card-heading"></i>
                                </span>
                                <input type="text" class="form-control input-lookup-special border-start-0" id="ruc" name="ruc" value="{{ old('ruc') }}"
                                    inputmode="numeric" minlength="11" maxlength="11" pattern="\d{11}" placeholder="11 dígitos">
                                <button class="btn btn-lookup-special px-4" type="button" id="lookup_ruc_btn">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="razon_social" class="form-label fw-semibold">Razón social</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social"
                                value="{{ old('razon_social') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" placeholder="RAZÓN SOCIAL">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="district" class="form-label fw-semibold">Distrito</label>
                            <input type="text" class="form-control" id="district" name="district"
                                value="{{ old('district') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" placeholder="DISTRITO" required>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="border rounded-3 p-3 bg-light shadow-sm">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-person-badge-fill text-primary fs-5"></i>
                                    <h6 class="mb-0 fw-bold text-primary text-uppercase small">Datos del Representante</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="representative_dni" class="form-label fw-semibold">DNI representante</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-lookup-special" id="representative_dni" name="representative_dni"
                                                value="{{ old('representative_dni') }}" inputmode="numeric" maxlength="10"
                                                placeholder="DNI">
                                            <button class="btn btn-lookup-special" type="button" id="lookup_representative_dni_btn_entities">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="representative_nombres" class="form-label fw-semibold">Nombres</label>
                                        <input type="text" class="form-control" id="representative_nombres" name="representative_nombres"
                                            value="{{ old('representative_nombres') }}" style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase();" placeholder="NOMBRES">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="representative_apellido_paterno" class="form-label fw-semibold">Apellido paterno</label>
                                        <input type="text" class="form-control" id="representative_apellido_paterno" name="representative_apellido_paterno"
                                            value="{{ old('representative_apellido_paterno') }}" style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO PATERNO">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="representative_apellido_materno" class="form-label fw-semibold">Apellido materno</label>
                                        <input type="text" class="form-control" id="representative_apellido_materno" name="representative_apellido_materno"
                                            value="{{ old('representative_apellido_materno') }}" style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO MATERNO">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="representative_cargo" class="form-label fw-semibold">Cargo</label>
                                        <input type="text" class="form-control" id="representative_cargo" name="representative_cargo"
                                            value="{{ old('representative_cargo') }}" style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase();" placeholder="CARGO">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="representative_since" class="form-label fw-semibold">Representante desde</label>
                                        <input type="date" class="form-control" id="representative_since" name="representative_since"
                                            value="{{ old('representative_since') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Guardar entidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
