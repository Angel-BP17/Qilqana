<div class="modal fade" id="editInteresadoModal" tabindex="-1" aria-labelledby="editInteresadoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editInteresadoModalLabel">Editar interesado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editInteresadoForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tipo de interesado</label>
                            <select class="form-select" id="edit_tipo_interesado" name="tipo_interesado" required>
                                <option value="">Selecciona un tipo</option>
                                @foreach ($tipoOptions as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 persona-juridica-fields-edit d-none">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">RUC</label>
                                    <input type="text" name="ruc" id="edit_ruc" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Razón social</label>
                                    <input type="text" name="razon_social" id="edit_razon_social" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 persona-natural-fields-edit d-none">
                            <div class="row g-3">
                                <div class="col-12 col-md-3">
                                    <label class="form-label">DNI</label>
                                    <input type="text" name="dni" id="edit_dni" class="form-control">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Nombres</label>
                                    <input type="text" name="nombres" id="edit_nombres" class="form-control">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Apellido paterno</label>
                                    <input type="text" name="apellido_paterno" id="edit_apellido_paterno" class="form-control">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Apellido materno</label>
                                    <input type="text" name="apellido_materno" id="edit_apellido_materno" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 cargo-fields-edit d-none">
                            <label class="form-label">Cargo</label>
                            <input type="text" name="cargo" id="edit_cargo" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
