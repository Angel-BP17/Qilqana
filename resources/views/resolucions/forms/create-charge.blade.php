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
                    {{-- Alert para errores de validación de Laravel --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-danger-subtle py-2 mb-4">
                            <h6 class="fw-bold mb-1 small text-danger d-flex align-items-center">
                                <span class="material-symbols-outlined fs-5 me-1">error</span>
                                Errores de Validación:
                            </h6>
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- SECCIÓN 1: RESOLUCIONES VINCULADAS --}}
                    <div class="card form-section-card mb-4" id="create_charge_res_sec_1">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="material-symbols-outlined text-primary me-2">link</span>
                                <h6 class="text-primary fw-bold mb-0">1. Resoluciones a Vincular</h6>
                            </div>
                            
                            {{-- Filtros de Fecha --}}
                            <div class="row g-2 mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="search_desde" class="form-label small text-muted fw-bold text-uppercase mb-1">Fecha Desde</label>
                                    <input type="date" class="form-control form-control-sm border-secondary-subtle bg-white" id="search_desde">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="search_hasta" class="form-label small text-muted fw-bold text-uppercase mb-1">Fecha Hasta</label>
                                    <input type="date" class="form-control form-control-sm border-secondary-subtle bg-white" id="search_hasta">
                                </div>
                            </div>

                            {{-- Input de Búsqueda Rápida --}}
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white"><span class="material-symbols-outlined text-muted fs-5">search</span></span>
                                <input type="text" class="form-control border-secondary-subtle bg-white" id="search_resolutions_input" placeholder="Busque por RD, nombre, DNI o asunto..." autocomplete="off">
                                <button class="btn btn-primary fw-bold px-4" type="button" id="btn_search_resolutions">Buscar</button>
                            </div>
 
                            {{-- Contenedor de Resultados del Buscador --}}
                            <div class="border rounded-2 bg-white p-0 overflow-hidden d-none mb-3" id="resolutions_results_container">
                                <div class="bg-light p-2 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-muted ms-1">Resultados de Búsqueda</span>
                                    <span class="badge bg-secondary rounded-pill" id="resolutions_results_count">0</span>
                                </div>
                                <div class="list-group list-group-flush" id="resolutions_list" style="max-height: 200px; overflow-y: auto;">
                                    {{-- Se renderizan aquí dinámicamente --}}
                                </div>
                            </div>

                            <div class="form-text mt-1 mb-3" id="resolutions_help_text">
                                <span class="material-symbols-outlined fs-6 align-middle me-1">info</span>Filtre por fecha o escriba para buscar resoluciones sin cargo en el sistema.
                            </div>

                            {{-- LISTA VISUAL DE RESOLUCIONES SELECCIONADAS (PERSISTENTE) --}}
                            <div class="border rounded-2 bg-white overflow-hidden" id="selected_resolutions_container">
                                <table class="table table-sm table-hover mb-0 align-middle" id="table_selected_resolutions">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3 py-2 small text-muted text-uppercase" style="width: 150px;">Resolución</th>
                                            <th class="py-2 small text-muted text-uppercase">Detalles (Interesado / Fecha)</th>
                                            <th class="text-end pe-3 py-2" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="list_selected_resolutions">
                                        <tr class="empty-resolutions-row">
                                            <td colspan="3" class="text-center py-4 text-muted small italic">
                                                <span class="material-symbols-outlined fs-2 d-block mb-1">link_off</span>
                                                No hay resoluciones añadidas a este cargo.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="hidden_resolutions_inputs"></div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: ¿PARA QUIÉNES DESEA CREAR LOS CARGOS? --}}
                    <div class="card form-section-card mb-4" id="create_charge_res_sec_2">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="material-symbols-outlined text-primary me-2">group</span>
                                <h6 class="text-primary fw-bold mb-0">2. ¿PARA QUIÉNES DESEA CREAR LOS CARGOS?</h6>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="form-check border rounded-3 p-3 bg-white d-flex align-items-center h-100">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="cargo_para" id="recipient_type_interesados" value="interesados_resolucion">
                                        <label class="form-check-label fw-bold text-dark cursor-pointer mb-0" for="recipient_type_interesados">
                                            Interesados de la resolución
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check border rounded-3 p-3 bg-white d-flex align-items-center h-100">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="cargo_para" id="recipient_type_otros" value="otros" checked>
                                        <label class="form-check-label fw-bold text-dark cursor-pointer mb-0" for="recipient_type_otros">
                                            Otros destinatarios
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center text-muted small" id="recipient_type_help_container">
                                <span class="material-symbols-outlined fs-5 me-2">info</span>
                                <span id="recipient_help_message">Se creará un cargo para cada destinatario que agregue manualmente a continuación.</span>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 3: IDENTIFICACIÓN DEL INTERESADO --}}
                    <div class="card form-section-card mb-4" id="recipient_manual_section">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="material-symbols-outlined text-primary me-2">person_pin</span>
                                <h6 class="text-primary fw-bold mb-0">3. Destinatario del Cargo</h6>
                            </div>
                                       <div class="row g-3 mb-2">
                                <div class="col-12 col-md-6">
                                    <label for="tipo_interesado" class="form-label fw-bold small text-muted text-uppercase">Clasificación</label>
                                    <select class="form-select border-secondary-subtle" id="tipo_interesado">
                                        <option value="">Seleccione...</option>
                                        <option value="Persona Natural">Persona Natural</option>
                                        <option value="Persona Juridica">Persona Juridica</option>
                                        <option value="Trabajador UGEL">Trabajador UGEL</option>
                                    </select>
                                </div>

                                {{-- Sub-selector para Persona Natural (DNI/Cédula) --}}
                                <div class="col-12 col-md-6 interessado-selector-field d-none" id="container_natural_doc_type">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Tipo Documento</label>
                                    <select class="form-select border-secondary-subtle" id="charge_natural_doc_type">
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

                                {{-- Buscador Trabajador UGEL --}}
                                <div class="interessado-selector-field d-none mt-3" id="container_ugel_user">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Búsqueda de Trabajador (DNI/Nombre)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-primary">
                                            <span class="material-symbols-outlined fs-5">badge</span>
                                        </span>
                                        <input type="text" class="form-control border-secondary-subtle" id="lookup_charge_ugel_value" placeholder="Ingrese DNI o nombre del trabajador...">
                                        <button class="btn btn-primary fw-bold" type="button" id="btn_lookup_charge_ugel">BUSCAR</button>
                                    </div>
                                    <div id="lookup_charge_ugel_error" class="text-danger small mt-1 d-none"></div>
                                </div>
                            </div>

                            {{-- Información del Destinatario Resuelto --}}
                            <div id="charge_recipient_info" class="mt-3 d-none">
                                <div class="alert alert-info border-primary-subtle d-flex align-items-center justify-content-between mb-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined text-primary me-3 fs-3">check_circle</span>
                                        <div>
                                            <div id="charge_recipient_name" class="fw-bold text-dark small"></div>
                                            <div id="charge_recipient_id_text" class="small text-muted" style="font-size: 0.85em;"></div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm fw-bold px-3 d-flex align-items-center gap-1 shadow-sm" id="btn_add_recipient_to_list">
                                        <span class="material-symbols-outlined fs-5">person_add</span>Agregar
                                    </button>
                                </div>
                            </div>

                            {{-- Lista de Destinatarios Agregados --}}
                            <div id="charge_recipient_list_wrapper" class="mt-4 d-none">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Destinatarios Agregados</label>
                                <div class="list-group shadow-sm border border-secondary-subtle rounded-3 overflow-hidden bg-white" id="charge_recipient_list_container">
                                    {{-- Se insertarán dinámicamente --}}
                                </div>
                                <div id="hidden_destinatarios_inputs"></div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 4: DETALLES DEL DOCUMENTO --}}
                    <div class="card form-section-card mb-4" id="create_charge_res_sec_4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="material-symbols-outlined text-primary me-2">edit_document</span>
                                <h6 class="text-primary fw-bold mb-0">4. Detalles del documento del cargo</h6>
                            </div>
                            
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('createChargeForm');
        if (!form) return;

        const sec1 = document.getElementById('create_charge_res_sec_1');
        const sec2 = document.getElementById('create_charge_res_sec_2');
        const sec3 = document.getElementById('recipient_manual_section');
        const sec4 = document.getElementById('create_charge_res_sec_4');

        // Función para validar si la sección 1 tiene al menos una resolución agregada
        const sec1HasResolutions = () => {
            const container = document.getElementById('hidden_resolutions_inputs');
            if (!container) return false;
            const inputs = container.getElementsByTagName('input');
            return inputs.length > 0;
        };

        // Función para validar la sección 2 (siempre es válida porque tiene radios y uno está seleccionado por defecto)
        const sec2Valid = () => {
            const checked = form.querySelector('input[name="cargo_para"]:checked');
            return !!checked;
        };

        // Función para validar la sección 3
        const sec3Valid = () => {
            // Si la sección de destinatario manual está oculta (porque es para interesados de la resolución), se considera válida
            if (sec3 && (sec3.style.display === 'none' || sec3.classList.contains('d-none'))) {
                return true;
            }
            // Si está visible, requiere tener al menos un destinatario agregado en el listado
            const container = document.getElementById('hidden_destinatarios_inputs');
            if (!container) return false;
            const inputs = container.getElementsByTagName('input');
            return inputs.length > 0;
        };

        // Campos obligatorios de la sección 4
        const sec4Fields = () => [
            document.getElementById('asunto')
        ];

        function checkValidity() {
            // Evaluar validez de cada sección
            const s1Valid = sec1HasResolutions();
            const s2Valid = sec2Valid();
            const s3Valid = sec3Valid();
            const s4Valid = sec4Fields().every(field => field && field.value.trim() !== '' && field.checkValidity());

            // Limpiar clases
            [sec1, sec2, sec3, sec4].forEach(sec => {
                if (sec) {
                    sec.classList.remove('active-section', 'completed-section');
                }
            });

            // Lógica progresiva en cascada
            if (!s1Valid) {
                if (sec1) sec1.classList.add('active-section');
            } else {
                if (sec1) sec1.classList.add('completed-section');

                if (!s2Valid) {
                    if (sec2) sec2.classList.add('active-section');
                } else {
                    if (sec2) sec2.classList.add('completed-section');

                    // Validar si la sección 3 está visible y requiere atención
                    const isSec3Visible = sec3 && sec3.style.display !== 'none' && !sec3.classList.contains('d-none');
                    if (isSec3Visible && !s3Valid) {
                        if (sec3) sec3.classList.add('active-section');
                    } else {
                        if (isSec3Visible && sec3) {
                            sec3.classList.add('completed-section');
                        }

                        if (!s4Valid) {
                            if (sec4) sec4.classList.add('active-section');
                        } else {
                            if (sec4) sec4.classList.add('completed-section');
                        }
                    }
                }
            }
        }

        // Escuchar inputs y cambios
        form.addEventListener('input', checkValidity);
        form.addEventListener('change', () => setTimeout(checkValidity, 50));

        // Observar cambios dinámicos en la lista de resoluciones
        const resContainer = document.getElementById('hidden_resolutions_inputs');
        if (resContainer) {
            const observer = new MutationObserver(checkValidity);
            observer.observe(resContainer, { childList: true });
        }

        // Observar cambios dinámicos en la lista de destinatarios
        const destContainer = document.getElementById('hidden_destinatarios_inputs');
        if (destContainer) {
            const observer = new MutationObserver(checkValidity);
            observer.observe(destContainer, { childList: true });
        }

        // Ejecutar validación inicial
        setTimeout(checkValidity, 500);

        // Re-evaluar cuando se abra el modal
        const modalEl = document.getElementById('createChargeModal');
        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', checkValidity);
        }
    });
</script>

