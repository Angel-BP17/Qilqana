<div class="modal fade" id="createChargeModal" tabindex="-1" aria-labelledby="createChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center" id="createChargeModalLabel">
                    <span class="material-symbols-outlined me-2 fs-4">note_add</span>Registrar Cargos Manuales
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('charges.store') }}" id="createChargeFormManual" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="cargo_para" value="otros">
                <div class="modal-body px-4 py-3">
                    
                    {{-- SECCIÓN 1: BÚSQUEDA Y AGREGACIÓN DE DESTINATARIOS --}}
                    <div class="card form-section-card mb-4 shadow-sm" id="create_charge_sec_1">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="material-symbols-outlined text-primary me-2">group_add</span>
                                <h6 class="text-primary fw-bold mb-0">Destinatarios del Cargo (Añadir múltiples interesados)</h6>
                            </div>
                            
                            <div class="row g-3 mb-2">
                                <div class="col-12 col-md-6">
                                    <label for="tipo_interesado_manual" class="form-label fw-bold small text-muted text-uppercase">Clasificación</label>
                                    <select class="form-select border-secondary-subtle" id="tipo_interesado_manual">
                                        <option value="">Seleccione...</option>
                                        <option value="Persona Natural">Persona Natural</option>
                                        <option value="Persona Juridica">Persona Juridica</option>
                                        <option value="Trabajador UGEL">Trabajador UGEL</option>
                                    </select>
                                </div>

                                {{-- Selector de documento de Persona Natural --}}
                                <div class="col-12 col-md-6 d-none" id="container_natural_doc_type_manual">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Tipo Documento</label>
                                    <select class="form-select border-secondary-subtle" id="charge_natural_doc_type_manual">
                                        <option value="DNI" selected>DNI</option>
                                        <option value="CEDULA">CÉDULA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3">
                                {{-- Buscador Persona Natural --}}
                                <div class="col-12 d-none mt-3" id="container_natural_person_manual">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Número de Identidad</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-primary">
                                            <span class="material-symbols-outlined fs-5">badge</span>
                                        </span>
                                        <input type="text" class="form-control border-secondary-subtle" id="lookup_natural_value_manual" placeholder="Ingrese número...">
                                        <button class="btn btn-outline-primary fw-bold" type="button" id="btn_lookup_natural_manual">BUSCAR</button>
                                    </div>
                                    <div id="lookup_natural_error_manual" class="text-danger small mt-1 d-none"></div>
                                </div>

                                {{-- Buscador Persona Jurídica --}}
                                <div class="col-12 d-none mt-3" id="container_legal_entity_manual">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Número RUC</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-primary">
                                            <span class="material-symbols-outlined fs-5">corporate_fare</span>
                                        </span>
                                        <input type="text" class="form-control border-secondary-subtle" id="lookup_legal_value_manual" placeholder="Ingrese RUC..." maxlength="11">
                                        <button class="btn btn-outline-primary fw-bold" type="button" id="btn_lookup_legal_manual">BUSCAR</button>
                                    </div>
                                    <div id="lookup_legal_error_manual" class="text-danger small mt-1 d-none"></div>
                                </div>

                                {{-- Buscador Trabajador UGEL --}}
                                <div class="col-12 d-none mt-3" id="container_ugel_user_manual">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Búsqueda de Trabajador</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-secondary-subtle text-primary">
                                            <span class="material-symbols-outlined fs-5">engineering</span>
                                        </span>
                                        <select class="form-select select2-ajax-users-manual border-secondary-subtle" id="select_ugel_user_manual">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Confirmación / Información del Destinatario Resuelto --}}
                            <div id="manual_resolved_info" class="mt-3 d-none">
                                <div class="alert alert-info border-info-subtle d-flex align-items-center justify-content-between mb-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined text-info me-3 fs-3">check_circle</span>
                                        <div>
                                            <div id="manual_resolved_name" class="fw-bold text-dark small"></div>
                                            <div id="manual_resolved_doc" class="small text-muted" style="font-size: 0.85em;"></div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm fw-bold px-3 d-flex align-items-center gap-1 shadow-sm" id="btn_add_destinatario_manual">
                                        <span class="material-symbols-outlined fs-5">person_add</span>Agregar
                                    </button>
                                </div>
                            </div>

                            {{-- Lista de Destinatarios Agregados --}}
                            <div id="manual_destinatarios_list_wrapper" class="mt-3 d-none">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Destinatarios Agregados</label>
                                <div class="list-group shadow-sm border border-secondary-subtle rounded-3 overflow-hidden bg-white" id="manual_destinatarios_agregados_lista">
                                    {{-- Los items se insertan aquí y se enlazan con inputs ocultos para el submit --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: DETALLES DEL DOCUMENTO --}}
                    <div class="card form-section-card mb-2 shadow-sm" id="create_charge_sec_2">
                        <div class="card-body p-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-3 d-flex align-items-center">
                                <span class="material-symbols-outlined fs-5 me-1 text-secondary">description</span>
                                Detalles del documento del cargo
                            </label>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-8">
                                    <label for="asunto_manual" class="form-label fw-bold small text-muted">Se remite (Asunto)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><span class="material-symbols-outlined fs-5 text-muted">forward_to_inbox</span></span>
                                        <input type="text" class="form-control text-uppercase border-secondary-subtle" id="asunto_manual" name="asunto" required
                                            placeholder="Ej: DOCUMENTOS ADJUNTOS">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="document_date_manual" class="form-label fw-bold small text-muted">Fecha Documento</label>
                                    <input type="date" class="form-control border-secondary-subtle" id="document_date_manual" name="document_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="document_file_manual" class="form-label fw-bold small text-muted">Documento de Cargo (PDF)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><span class="material-symbols-outlined fs-5 text-muted">picture_as_pdf</span></span>
                                        <input type="file" class="form-control border-secondary-subtle" id="document_file_manual" name="document_file" accept=".pdf">
                                    </div>
                                    <div class="form-text">
                                        Opcional. Tamaño máximo permitido: <strong>{{ (int) \App\Models\Setting::getValue('charges_max_file_size', '5120') / 1024 }}MB</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm d-flex align-items-center gap-1" id="btn_submit_manual">
                        <span class="material-symbols-outlined fs-5">save</span>Guardar Cargos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('createChargeFormManual');
        if (!form) return;

        const sec1 = document.getElementById('create_charge_sec_1');
        const sec2 = document.getElementById('create_charge_sec_2');

        // Función para validar si la sección 1 tiene al menos un destinatario agregado en el listado
        const sec1HasDestinatarios = () => {
            const list = document.getElementById('manual_destinatarios_agregados_lista');
            if (!list) return false;
            // Contamos los elementos con input hidden (ej. name="destinatarios[...]...")
            const inputs = list.getElementsByTagName('input');
            return inputs.length > 0;
        };

        // Campos obligatorios de la sección 2
        const sec2Fields = () => [
            document.getElementById('asunto_manual')
        ];

        function checkValidity() {
            // Evaluar Sección 1
            const sec1Valid = sec1HasDestinatarios();
            
            // Evaluar Sección 2
            const sec2Valid = sec2Fields().every(field => field && field.value.trim() !== '' && field.checkValidity());

            // Limpiar clases
            [sec1, sec2].forEach(sec => {
                if (sec) {
                    sec.classList.remove('active-section', 'completed-section');
                }
            });

            // Lógica progresiva
            if (!sec1Valid) {
                if (sec1) sec1.classList.add('active-section');
            } else {
                if (sec1) sec1.classList.add('completed-section');

                if (!sec2Valid) {
                    if (sec2) sec2.classList.add('active-section');
                } else {
                    if (sec2) sec2.classList.add('completed-section');
                }
            }
        }

        // Escuchar inputs y cambios
        form.addEventListener('input', checkValidity);
        form.addEventListener('change', checkValidity);

        // Escuchar cambios dinámicos en la lista de destinatarios
        const destList = document.getElementById('manual_destinatarios_agregados_lista');
        if (destList) {
            const observer = new MutationObserver(checkValidity);
            observer.observe(destList, { childList: true });
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
