<div class="modal fade" id="createChargeModal" tabindex="-1" aria-labelledby="createChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="createChargeModalLabel">
                    <span class="material-symbols-outlined me-2">note_add</span>Registrar cargo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('charges.store') }}" id="createChargeForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    {{-- SECCIÓN 1: IDENTIFICACIÓN DEL INTERESADO --}}
                    <div class="card border-primary-subtle bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="material-symbols-outlined text-primary me-2">person_pin</span>
                                <h6 class="text-primary fw-bold mb-0">Destinatario del Cargo</h6>
                            </div>
                            
                            <div class="row g-3 mb-2">
                                <div class="col-12 col-md-6">
                                    <label for="tipo_interesado" class="form-label fw-bold small text-muted text-uppercase">Clasificación</label>
                                    <select class="form-select border-secondary-subtle" id="tipo_interesado" name="tipo_interesado" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Persona Natural">Persona Natural</option>
                                        <option value="Persona Juridica">Persona Juridica</option>
                                        <option value="Trabajador UGEL">Trabajador UGEL</option>
                                    </select>
                                </div>

                                {{-- Sub-selector para Persona Natural (DNI/Cédula) --}}
                                <div class="col-12 col-md-6 interessado-selector-field d-none" id="container_natural_doc_type">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Tipo Documento</label>
                                    <select class="form-select border-secondary-subtle" id="charge_natural_doc_type" name="document_type">
                                        <option value="DNI" selected>DNI</option>
                                        <option value="CEDULA">CÉDULA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3">
                                {{-- Buscador Persona Natural (Manual) --}}
                                <div class="interessado-selector-field d-none mt-3" id="container_natural_person">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Número de Identidad</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-primary">
                                            <span class="material-symbols-outlined fs-5">badge</span>
                                        </span>
                                        <input type="text" class="form-control border-secondary-subtle" id="lookup_charge_natural_value" placeholder="Ingrese número...">
                                        <button class="btn btn-primary fw-bold" type="button" id="btn_lookup_charge_natural">BUSCAR</button>
                                    </div>
                                    <div id="lookup_charge_natural_error" class="text-danger small mt-1 d-none"></div>
                                </div>

                                {{-- Buscador Persona Jurídica (Manual) --}}
                                <div class="interessado-selector-field d-none mt-3" id="container_legal_entity">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Número RUC</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-primary">
                                            <span class="material-symbols-outlined fs-5">corporate_fare</span>
                                        </span>
                                        <input type="text" class="form-control border-secondary-subtle" id="lookup_charge_legal_value" placeholder="Ingrese RUC..." maxlength="11">
                                        <button class="btn btn-primary fw-bold" type="button" id="btn_lookup_charge_legal">BUSCAR</button>
                                    </div>
                                    <div id="lookup_charge_legal_error" class="text-danger small mt-1 d-none"></div>
                                </div>

                                {{-- Buscador Trabajador UGEL (Se mantiene Select2) --}}
                                <div class="interessado-selector-field d-none mt-3" id="container_ugel_user">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Búsqueda de Trabajador</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-primary">
                                            <span class="material-symbols-outlined fs-5">engineering</span>
                                        </span>
                                        <select class="form-select select2-ajax-users border-secondary-subtle" id="select_ugel_user" name="assigned_to">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Información del Destinatario Seleccionado --}}
                            <div id="charge_recipient_info" class="mt-3 d-none">
                                <div class="alert alert-info border-primary-subtle d-flex align-items-center mb-0">
                                    <span class="material-symbols-outlined me-3 fs-3">check_circle</span>
                                    <div>
                                        <div class="fw-bold small text-uppercase">Destinatario Confirmado:</div>
                                        <div id="charge_recipient_name" class="fw-bold text-dark"></div>
                                        <div id="charge_recipient_id_text" class="small text-muted"></div>
                                    </div>
                                </div>
                                {{-- Inputs ocultos para el envío --}}
                                <input type="hidden" name="natural_person_id" id="hidden_charge_natural_id">
                                <input type="hidden" name="legal_entity_id" id="hidden_charge_legal_id">
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: RESOLUCIONES VINCULADAS (Búsqueda Checkbox) --}}
                    <div class="card border-primary-subtle bg-light-subtle mb-4">
                        <div class="card-body p-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                                <span class="material-symbols-outlined fs-6 align-middle me-1">link</span>Resoluciones a Vincular
                            </label>
                            
                            {{-- Input de Búsqueda --}}
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white"><span class="material-symbols-outlined text-muted fs-5">search</span></span>
                                <input type="text" class="form-control" id="search_resolutions_input" placeholder="Busque por RD, nombre, DNI o asunto..." autocomplete="off">
                                <button class="btn btn-primary fw-bold" type="button" id="btn_search_resolutions">Buscar</button>
                            </div>

                            {{-- Contenedor de Resultados con Checkboxes --}}
                            <div class="border rounded-2 bg-white p-0 overflow-hidden d-none" id="resolutions_results_container">
                                <div class="bg-light p-2 border-bottom d-flex justify-content-between align-items-center">
                                    <div class="form-check m-0 ms-1">
                                        <input class="form-check-input" type="checkbox" id="select_all_resolutions">
                                        <label class="form-check-label small fw-bold text-muted" for="select_all_resolutions">
                                            Seleccionar todas las visibles
                                        </label>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill" id="resolutions_results_count">0</span>
                                </div>
                                <div class="list-group list-group-flush" id="resolutions_list" style="max-height: 250px; overflow-y: auto;">
                                    {{-- Los items se insertan aquí vía JS --}}
                                </div>
                            </div>
                            
                            <div class="form-text mt-2" id="resolutions_help_text">
                                <span class="material-symbols-outlined fs-6 align-middle me-1">info</span>Escriba el número o nombre para buscar resoluciones sin cargo en el sistema.
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 3: DETALLES DEL DOCUMENTO --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-8">
                            <label for="asunto" class="form-label fw-bold small text-muted text-uppercase">Se remite (Asunto)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><span class="material-symbols-outlined fs-5">forward_to_inbox</span></span>
                                <input type="text" class="form-control text-uppercase" id="asunto" name="asunto" required
                                    placeholder="Ej: DOCUMENTOS ADJUNTOS">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="document_date" class="form-label fw-bold small text-muted text-uppercase">Fecha Documento</label>
                            <input type="date" class="form-control" id="document_date" name="document_date"
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="document_file" class="form-label fw-bold small text-muted text-uppercase">Documento de Cargo (PDF)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><span class="material-symbols-outlined fs-5">picture_as_pdf</span></span>
                                <input type="file" class="form-control" id="document_file" name="document_file" accept=".pdf">
                            </div>
                            <div class="form-text">
                                Opcional. Tamaño máximo permitido: <strong>{{ (int) \App\Models\Setting::getValue('charges_max_file_size', '5120') / 1024 }}MB</strong>.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <span class="material-symbols-outlined align-middle me-1">save</span>Guardar Cargo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
