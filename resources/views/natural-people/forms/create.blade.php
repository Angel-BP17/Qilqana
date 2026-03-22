<div class="modal fade" id="createNaturalPersonModal" tabindex="-1" aria-labelledby="createNaturalPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold" id="createNaturalPersonModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar persona natural
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('natural-people.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="dni" class="form-label fw-semibold">DNI</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-card-text"></i>
                                </span>
                                <input type="text" class="form-control input-lookup-special border-start-0" id="dni" name="dni"
                                    value="{{ old('dni') }}" minlength="8" maxlength="10" inputmode="numeric"
                                    pattern="\d{8,10}" placeholder="Ingrese DNI">
                                <button class="btn btn-lookup-special px-4" type="button" id="lookup_dni_btn">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="text-muted opacity-25">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="nombres" class="form-label fw-semibold">Nombres</label>
                            <input type="text" class="form-control" id="nombres" name="nombres"
                                value="{{ old('nombres') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" placeholder="NOMBRES">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="apellido_paterno" class="form-label fw-semibold">Apellido paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno"
                                value="{{ old('apellido_paterno') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO PATERNO">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="apellido_materno" class="form-label fw-semibold">Apellido materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                                value="{{ old('apellido_materno') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" placeholder="APELLIDO MATERNO">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Guardar datos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
