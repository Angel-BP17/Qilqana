@php
    $formAction = $action ?? '#';
    $res = $resolucion ?? null;
    $fechaValue = $res?->fecha ? $res->fecha->format('Y-m-d') : '';
@endphp

<form id="editResolutionForm" method="POST" action="{{ $formAction }}">
    @csrf
    @method('PUT')
    <div class="modal-body px-4">
        
        {{-- NIVEL 1: TIPO DE RESOLUCIÓN --}}
        <div class="mb-4 pb-3 border-bottom">
            <h6 class="text-warning mb-3"><span class="material-symbols-outlined align-middle me-2">account_tree</span>1. Categorización de la Resolución</h6>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small text-muted fw-bold text-uppercase">Tipo de Resolución</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">category</span></span>
                        <select name="resolucion_type_id" class="form-select border-warning-subtle bg-white" id="edit_resolution_type" required>
                            <option value="">Seleccione tipo...</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" {{ ($res?->resolucion_type_id == $type->id) ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- NIVEL 2: PANEL DE INTERESADOS (MÚLTIPLES) --}}
        <div class="mb-4 pb-3 border-bottom">
            <h6 class="text-warning mb-3"><span class="material-symbols-outlined align-middle me-2">group_add</span>2. Destinatarios / Interesados</h6>
            
            <div class="card border-warning-subtle bg-light-subtle shadow-sm mb-3">
                <div class="card-body p-3">
                    {{-- Selector de Tipo --}}
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Clasificación</label>
                            <select class="form-select border-secondary-subtle" id="edit_selector_interesado_type">
                                <option value="Persona Natural">Persona Natural</option>
                                <option value="Persona Juridica">Persona Juridica</option>
                                <option value="Trabajador UGEL">Trabajador UGEL</option>
                            </select>
                        </div>
                    </div>

                    {{-- BLOQUE PERSONA NATURAL (Búsqueda Manual) --}}
                    <div id="edit_wrapper_natural_search" class="interessado-form-block">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label small text-muted fw-bold text-uppercase">Documento</label>
                                <select class="form-select border-secondary-subtle" id="edit_lookup_natural_doc_type">
                                    <option value="DNI">DNI</option>
                                    <option value="CEDULA">CÉDULA</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="form-label small text-muted fw-bold text-uppercase">Número de Identidad</label>
                                <div class="input-group">
                                    <input type="text" class="form-control border-secondary-subtle" id="edit_lookup_natural_value" placeholder="Ingrese número...">
                                    <button class="btn btn-warning fw-bold text-dark" type="button" id="edit_btn_lookup_natural">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mt-2 d-none" id="edit_result_natural_details">
                            <div class="col-12">
                                <div class="alert alert-info border-0 py-2 mb-2 d-flex align-items-center">
                                    <span class="material-symbols-outlined me-2 text-dark">person</span>
                                    <span id="edit_text_natural_result" class="fw-bold small text-uppercase text-dark"></span>
                                </div>
                                <button type="button" class="btn btn-success btn-sm fw-bold w-100" id="edit_btn_add_natural_list">
                                    <span class="material-symbols-outlined align-middle fs-6">person_add</span> AÑADIR A LA LISTA
                                </button>
                            </div>
                        </div>
                        <div id="edit_lookup_natural_error" class="text-danger small mt-1 d-none"></div>
                    </div>

                    {{-- BLOQUE PERSONA JURIDICA (Búsqueda Manual) --}}
                    <div id="edit_wrapper_legal_search" class="interessado-form-block d-none">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-10">
                                <label class="form-label small text-muted fw-bold text-uppercase">RUC</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-secondary-subtle"><span class="material-symbols-outlined fs-5">corporate_fare</span></span>
                                    <input type="text" class="form-control border-secondary-subtle" id="edit_lookup_legal_value" placeholder="Ingrese RUC..." maxlength="11">
                                    <button class="btn btn-warning fw-bold text-dark" type="button" id="edit_btn_lookup_legal">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mt-2 d-none" id="edit_result_legal_details">
                            <div class="col-12">
                                <div class="alert alert-info border-0 py-2 mb-2 d-flex align-items-center">
                                    <span class="material-symbols-outlined me-2 text-dark">business</span>
                                    <span id="edit_text_legal_result" class="fw-bold small text-uppercase text-dark"></span>
                                </div>
                                <button type="button" class="btn btn-success btn-sm fw-bold w-100" id="edit_btn_add_legal_list">
                                    <span class="material-symbols-outlined align-middle fs-6">domain_add</span> AÑADIR A LA LISTA
                                </button>
                            </div>
                        </div>
                        <div id="edit_lookup_legal_error" class="text-danger small mt-1 d-none"></div>
                    </div>

                    {{-- BLOQUE TRABAJADOR UGEL (Select2 AJAX Dinámico) --}}
                    <div id="edit_wrapper_ugel_search" class="interessado-form-block d-none">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-10">
                                <label class="form-label small text-muted fw-bold text-uppercase">Seleccione Trabajador</label>
                                <select class="form-select select2-ajax-users" id="edit_search_ugel_interesado"></select>
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-warning w-100 fw-bold text-dark" id="edit_btn_add_ugel_list">AÑADIR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LISTA VISUAL DE INTERESADOS SELECCIONADOS --}}
            <div class="border rounded-2 bg-white overflow-hidden" id="edit_selected_interesados_container">
                <table class="table table-sm table-hover mb-0 align-middle" id="edit_table_selected_interesados">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-2 small text-muted text-uppercase" style="width: 150px;">Tipo</th>
                            <th class="py-2 small text-muted text-uppercase">Nombre / Razón Social</th>
                            <th class="py-2 small text-muted text-uppercase" style="width: 150px;">Identidad</th>
                            <th class="text-end pe-3 py-2" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="edit_list_selected_interesados">
                    </tbody>
                </table>
            </div>
            <div id="edit_hidden_interesados_inputs"></div>
        </div>

        {{-- NIVEL 3: DETALLES DE LA RESOLUCIÓN --}}
        <div>
            <h6 class="text-warning mb-3"><span class="material-symbols-outlined align-middle me-2">edit_document</span>3. Detalles del Documento</h6>
            
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-8">
                    <label class="form-label small text-muted fw-bold text-uppercase">Número de Resolución (RD)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">tag</span></span>
                        <input type="text" class="form-control text-uppercase fw-bold" name="rd" id="edit_resolution_rd"
                            placeholder="EJ: 001 O 001-2026" value="{{ old('rd', $res?->rd ?? '') }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted fw-bold text-uppercase">Fecha Emisión</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">calendar_today</span></span>
                        <input type="date" class="form-control" name="fecha" id="edit_resolution_fecha"
                            value="{{ old('fecha', $fechaValue) }}" required>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small text-muted fw-bold text-uppercase">Categoría del Asunto</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">subject</span></span>
                        <select name="asunto_type_id" class="form-select border-warning-subtle bg-white" id="edit_asunto_type" disabled required data-selected="{{ $res?->asunto_type_id ?? '' }}">
                            <option value="">Seleccione tipo de resolución primero...</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label for="edit_resolution_asunto" class="form-label small text-muted fw-bold text-uppercase">Asunto / Resumen</label>
                    <textarea class="form-control text-uppercase" name="asunto" id="edit_resolution_asunto" rows="2"
                        placeholder="INGRESE DETALLES ADICIONALES (OPCIONAL)" required>{{ old('asunto', $res?->asunto ?? '') }}</textarea>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label for="edit_resolution_procedencia" class="form-label small text-muted fw-bold text-uppercase">Procedencia / Oficina de Origen</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-outlined fs-5">business_center</span></span>
                        <input type="text" class="form-control text-uppercase" name="procedencia" id="edit_resolution_procedencia"
                            placeholder="EJ: DIRECCIÓN / ÁREA DE GESTIÓN" value="{{ old('procedencia', $res?->procedencia ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal-footer bg-light px-4 border-top">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
            <span class="material-symbols-outlined align-middle me-1">save</span>Guardar Cambios
        </button>
    </div>
</form>
