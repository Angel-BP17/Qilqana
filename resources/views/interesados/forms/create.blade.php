<div class="modal fade" id="createInteresadoModal" tabindex="-1" aria-labelledby="createInteresadoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createInteresadoModalLabel">Registrar interesado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('interesados.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tipo de interesado</label>
                            <select class="form-select" id="tipo_interesado" name="tipo_interesado" required>
                                <option value="">Selecciona un tipo</option>
                                @foreach ($tipoOptions as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 persona-juridica-fields d-none">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">RUC</label>
                                    <input type="text" name="ruc" class="form-control" placeholder="RUC">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Razón social</label>
                                    <input type="text" name="razon_social" class="form-control" placeholder="Razón social">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 persona-natural-fields d-none">
                            <div class="row g-3">
                                <div class="col-12 col-md-3">
                                    <label class="form-label">DNI</label>
                                    <input type="text" name="dni" class="form-control" placeholder="DNI">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Nombres</label>
                                    <input type="text" name="nombres" class="form-control" placeholder="Nombres">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Apellido paterno</label>
                                    <input type="text" name="apellido_paterno" class="form-control" placeholder="Apellido paterno">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Apellido materno</label>
                                    <input type="text" name="apellido_materno" class="form-control" placeholder="Apellido materno">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 cargo-fields d-none">
                            <label class="form-label">Cargo</label>
                            <input type="text" name="cargo" class="form-control" placeholder="Cargo">
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
