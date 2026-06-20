<div class="modal fade" id="createChargeModal" tabindex="-1" aria-labelledby="createChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="createChargeModalLabel">
                    <span class="material-symbols-outlined me-2">note_add</span>Registrar cargo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('charges.store') }}" id="createChargeFormManual" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipo_interesado_manual" class="form-label fw-bold">Tipo de interesado</label>
                            <select class="form-select" id="tipo_interesado_manual" name="tipo_interesado" required>
                                <option value="">Seleccione</option>
                                <option value="Persona Juridica">Persona Juridica</option>
                                <option value="Persona Natural">Persona Natural</option>
                                <option value="Trabajador UGEL">Trabajador UGEL</option>
                            </select>
                        </div>
                        <div class="col-md-6 assigned-user-field d-none">
                            <label for="assigned_to" class="form-label fw-bold">Enviar a</label>
                            <select class="form-select select2-user" id="assigned_to" name="assigned_to">
                                <option value="">No enviar (quedara sin asignar)</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name . ' ' . $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- SECCIÓN: PERSONA JURÍDICA --}}
                    <div class="persona-juridica-fields d-none mt-3">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="ruc" class="form-label fw-bold">RUC</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ruc" name="ruc" maxlength="11">
                                    <button class="btn btn-outline-primary" type="button" id="lookup_charge_ruc_btn">Buscar</button>
                                </div>
                                <div id="ruc_api_error" class="text-danger small mt-1 d-none"></div>
                            </div>
                            <div class="col-md-6 entity-details d-none">
                                <label for="razon_social" class="form-label">Razon social</label>
                                <input type="text" class="form-control" id="razon_social" name="razon_social" readonly>
                            </div>
                            <div class="col-md-6 entity-details d-none">
                                <label for="district" class="form-label">Distrito</label>
                                <input type="text" class="form-control" id="district" name="district" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN: PERSONA NATURAL --}}
                    <div class="persona-natural-fields d-none mt-3">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="dni" class="form-label fw-bold">DNI</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dni" name="dni" maxlength="10">
                                    <button class="btn btn-outline-primary" type="button" id="lookup_charge_dni_btn">Buscar</button>
                                </div>
                                <div id="dni_api_error" class="text-danger small mt-1 d-none"></div>
                            </div>
                            <div class="col-md-4 natural-details d-none">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres">
                            </div>
                            <div class="col-md-4 natural-details d-none">
                                <label for="apellido_paterno" class="form-label">Ap. Paterno</label>
                                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno">
                            </div>
                            <div class="col-md-4 natural-details d-none">
                                <label for="apellido_materno" class="form-label">Ap. Materno</label>
                                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-8">
                            <label for="asunto_manual" class="form-label fw-bold">Se remite</label>
                            <input type="text" class="form-control text-uppercase" id="asunto_manual" name="asunto" required>
                        </div>
                        <div class="col-md-4">
                            <label for="document_date_manual" class="form-label fw-bold">Fecha del documento</label>
                            <input type="date" class="form-control" id="document_date_manual" name="document_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-12">
                            <label for="document_file_manual" class="form-label fw-bold">Documento del Cargo (PDF)</label>
                            <input type="file" class="form-control" id="document_file_manual" name="document_file" accept=".pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar cargo</button>
                </div>
            </form>
        </div>
    </div>
</div>
