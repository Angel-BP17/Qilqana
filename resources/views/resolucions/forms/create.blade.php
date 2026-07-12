<form id="createResolutionForm" method="POST" action="{{ route('resolucions.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="action_origin" value="create">
    <div class="modal-body px-4">
        {{-- Alert para errores de validación de Laravel --}}
        @if ($errors->any())
            <div class="alert alert-danger border-danger-subtle py-2 mb-4">
                <h6 class="fw-bold mb-1 small text-danger d-flex align-items-center">
                    <span class="material-symbols-outlined fs-5 me-1">error</span>
                    Errores de Validación (Creación):
                </h6>
                <ul class="mb-0 small ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- NIVEL 1: TIPO DE RESOLUCIÓN --}}
        <div class="mb-4 pb-3 border-bottom">
            <h6 class="text-primary mb-3"><span class="material-symbols-outlined align-middle me-2">account_tree</span>1. Categorización Principal</h6>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small text-muted fw-bold text-uppercase">Tipo de Resolución</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">category</span></span>
                        <select name="resolucion_type_id" class="form-select border-primary-subtle bg-white" id="create_resolution_type" required>
                            <option value="">Seleccione tipo...</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- NIVEL 2: PANEL DE INTERESADOS (MÚLTIPLES) --}}
        <div class="mb-4 pb-3 border-bottom">
            <h6 class="text-primary mb-3"><span class="material-symbols-outlined align-middle me-2">group_add</span>2. Destinatarios / Interesados</h6>
            
            <div class="card border-primary-subtle bg-light-subtle shadow-sm mb-3">
                <div class="card-body p-3">
                    {{-- Selector de Tipo --}}
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Clasificación</label>
                            <select class="form-select border-secondary-subtle" id="selector_interesado_type">
                                <option value="Persona Natural">Persona Natural</option>
                                <option value="Persona Juridica">Persona Juridica</option>
                                <option value="Trabajador UGEL">Trabajador UGEL</option>
                            </select>
                        </div>
                    </div>

                    {{-- BLOQUE PERSONA NATURAL (Búsqueda Manual) --}}
                    <div id="wrapper_natural_search" class="interessado-form-block">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label small text-muted fw-bold text-uppercase">Documento</label>
                                <select class="form-select border-secondary-subtle" id="lookup_natural_doc_type">
                                    <option value="DNI">DNI</option>
                                    <option value="CEDULA">CÉDULA</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="form-label small text-muted fw-bold text-uppercase">Número de Identidad</label>
                                <div class="input-group">
                                    <input type="text" class="form-control border-secondary-subtle" id="lookup_natural_value" placeholder="Ingrese número...">
                                    <button class="btn btn-primary fw-bold" type="button" id="btn_lookup_natural">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mt-2 d-none" id="result_natural_details">
                            <div class="col-12">
                                <div class="alert alert-info border-0 py-2 mb-2 d-flex align-items-center">
                                    <span class="material-symbols-outlined me-2">person</span>
                                    <span id="text_natural_result" class="fw-bold small text-uppercase"></span>
                                </div>
                                <button type="button" class="btn btn-success btn-sm fw-bold w-100" id="btn_add_natural_list">
                                    <span class="material-symbols-outlined align-middle fs-6">person_add</span> AÑADIR A LA LISTA
                                </button>
                            </div>
                        </div>

                        {{-- Registro Manual Persona Natural --}}
                        <div id="wrapper_natural_manual" class="mt-3 d-none border-top pt-3">
                            <p class="small text-danger fw-bold mb-2">Persona no encontrada. Regístrela manualmente:</p>
                            <div class="row g-2">
                                <div class="col-12 col-md-4">
                                    <input type="text" class="form-control form-control-sm text-uppercase" id="manual_natural_nombres" placeholder="Nombres">
                                </div>
                                <div class="col-12 col-md-4">
                                    <input type="text" class="form-control form-control-sm text-uppercase" id="manual_natural_paterno" placeholder="Ap. Paterno">
                                </div>
                                <div class="col-12 col-md-4">
                                    <input type="text" class="form-control form-control-sm text-uppercase" id="manual_natural_materno" placeholder="Ap. Materno">
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="button" class="btn btn-dark btn-sm fw-bold w-100" id="btn_save_manual_natural">
                                        <span class="material-symbols-outlined align-middle fs-6">save</span> REGISTRAR Y AÑADIR
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="lookup_natural_error" class="text-danger small mt-1 d-none"></div>
                    </div>

                    {{-- BLOQUE PERSONA JURIDICA (Búsqueda Manual) --}}
                    <div id="wrapper_legal_search" class="interessado-form-block d-none">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-10">
                                <label class="form-label small text-muted fw-bold text-uppercase">RUC</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-secondary-subtle"><span class="material-symbols-outlined fs-5">corporate_fare</span></span>
                                    <input type="text" class="form-control border-secondary-subtle" id="lookup_legal_value" placeholder="Ingrese RUC..." maxlength="11">
                                    <button class="btn btn-primary fw-bold" type="button" id="btn_lookup_legal">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mt-2 d-none" id="result_legal_details">
                            <div class="col-12">
                                <div class="alert alert-info border-0 py-2 mb-2 d-flex align-items-center">
                                    <span class="material-symbols-outlined me-2">business</span>
                                    <span id="text_legal_result" class="fw-bold small text-uppercase"></span>
                                </div>
                                <button type="button" class="btn btn-success btn-sm fw-bold w-100" id="btn_add_legal_list">
                                    <span class="material-symbols-outlined align-middle fs-6">domain_add</span> AÑADIR A LA LISTA
                                </button>
                            </div>
                        </div>

                        {{-- Registro Manual Persona Juridica --}}
                        <div id="wrapper_legal_manual" class="mt-3 d-none border-top pt-3">
                            <p class="small text-danger fw-bold mb-2">Entidad no encontrada. Regístrela manualmente:</p>
                            <div class="row g-2">
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-sm text-uppercase" id="manual_legal_razon_social" placeholder="Razón Social">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="text" class="form-control form-control-sm text-uppercase" id="manual_legal_district" placeholder="Distrito (Opcional)">
                                </div>
                                <div class="col-12 col-md-6 mt-md-0 mt-2">
                                    <button type="button" class="btn btn-dark btn-sm fw-bold w-100" id="btn_save_manual_legal">
                                        <span class="material-symbols-outlined align-middle fs-6">save</span> REGISTRAR Y AÑADIR
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="lookup_legal_error" class="text-danger small mt-1 d-none"></div>
                    </div>

                    {{-- BLOQUE TRABAJADOR UGEL (Select2 AJAX Dinámico) --}}
                    <div id="wrapper_ugel_search" class="interessado-form-block d-none">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-10">
                                <label class="form-label small text-muted fw-bold text-uppercase">Seleccione Trabajador</label>
                                <select class="form-select select2-ajax-users" id="search_ugel_interesado"></select>
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-primary w-100 fw-bold" id="btn_add_ugel_list">AÑADIR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LISTA VISUAL DE INTERESADOS SELECCIONADOS --}}
            <div class="border rounded-2 bg-white overflow-hidden" id="selected_interesados_container">
                <table class="table table-sm table-hover mb-0 align-middle" id="table_selected_interesados">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-2 small text-muted text-uppercase" style="width: 150px;">Tipo</th>
                            <th class="py-2 small text-muted text-uppercase">Nombre / Razón Social</th>
                            <th class="py-2 small text-muted text-uppercase" style="width: 150px;">Identidad</th>
                            <th class="text-end pe-3 py-2" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="list_selected_interesados">
                        <tr class="empty-interesados-row">
                            <td colspan="4" class="text-center py-4 text-muted small italic">
                                <span class="material-symbols-outlined fs-2 d-block mb-1">person_off</span>
                                No hay interesados añadidos a esta resolución.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="hidden_interesados_inputs"></div>
        </div>

        {{-- NIVEL 3: DETALLES DE LA RESOLUCIÓN --}}
        <div>
            <h6 class="text-primary mb-3"><span class="material-symbols-outlined align-middle me-2">edit_document</span>3. Detalles del Documento</h6>
            
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-8">
                    <label class="form-label small text-muted fw-bold text-uppercase">Número de Resolución (RD)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">tag</span></span>
                        <input type="text" class="form-control text-uppercase fw-bold" name="rd" id="create_resolution_rd"
                            placeholder="EJ: 001 O 001-2026" value="{{ old('rd') }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted fw-bold text-uppercase">Fecha Emisión</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">calendar_today</span></span>
                        <input type="date" class="form-control" name="fecha" id="create_resolution_fecha"
                            value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small text-muted fw-bold text-uppercase">Categoría del Asunto</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">subject</span></span>
                        <select name="asunto_type_id" class="form-select border-primary-subtle bg-white" id="create_asunto_type" disabled required>
                            <option value="">Seleccione tipo de resolución primero...</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small text-muted fw-bold text-uppercase">Modalidad / Nivel</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">school</span></span>
                        <select name="level_modality_id" class="form-select border-primary-subtle bg-white" id="create_level_modality">
                            <option value="">Seleccione modalidad / nivel (opcional)...</option>
                            @foreach ($level_modalities as $modality)
                                <option value="{{ $modality->id }}">{{ $modality->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label for="create_resolution_asunto" class="form-label small text-muted fw-bold text-uppercase">Asunto / Resumen</label>
                    <textarea class="form-control text-uppercase" name="asunto" id="create_resolution_asunto" rows="2"
                        placeholder="INGRESE DETALLES ADICIONALES (OPCIONAL)" required>{{ old('asunto') }}</textarea>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label for="create_resolution_procedencia" class="form-label small text-muted fw-bold text-uppercase">Procedencia / Oficina de Origen</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">business_center</span></span>
                        <input type="text" class="form-control text-uppercase" name="procedencia" id="create_resolution_procedencia"
                            placeholder="EJ: DIRECCIÓN / ÁREA DE GESTIÓN" value="{{ old('procedencia') }}">
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label for="create_resolution_file" class="form-label small text-muted fw-bold text-uppercase">Documento de Resolución (PDF)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><span class="material-symbols-outlined fs-5 text-muted">picture_as_pdf</span></span>
                        <input type="file" class="form-control border-secondary-subtle" id="create_resolution_file" name="document_file" accept=".pdf">
                    </div>
                    <div class="form-text">
                        Opcional. Tamaño máximo permitido: <strong>{{ (int) \App\Models\Setting::getValue('charges_max_file_size', '5') }}MB</strong>.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal-footer bg-light px-4 border-top">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm">
            <span class="material-symbols-outlined align-middle me-1">check_circle</span>Registrar Resolución
        </button>
    </div>
</form>
