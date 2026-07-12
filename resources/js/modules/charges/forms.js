import { route } from '../common/url-helper';
import { ApiLookup } from '../common/api-lookup';

export const FormModule = {
    init: function() {
        // Inicializar conjunto de resoluciones seleccionadas
        this.selectedResolutions = new Set();
        this.addedRecipients = [];
        this.tempRecipient = null;

        // 1. Inicializar Lógica DINÁMICA (Módulo de Resoluciones)
        this.setupDynamicLogic();

        // 2. Inicializar Lógica MANUAL (Módulo de Cargos)
        this.setupManualLogic();
        
        // 3. Inicializar Select2 Estáticos (Edición)
        this.initStaticSelect2();
    },

    /**
     * Lógica para el formulario DINÁMICO (Resoluciones -> Crear Cargo)
     */
    setupDynamicLogic: function() {
        const self = this;
        const typeSelect = document.getElementById('tipo_interesado');
        if (!typeSelect) return;

        // Limpiar estado al abrir modal
        $('#createChargeModal').on('show.bs.modal', () => {
            this.clearChargeRecipient();
            this.clearAllAddedRecipients();
            document.getElementById('lookup_charge_natural_value').value = '';
            document.getElementById('lookup_charge_legal_value').value = '';
            document.getElementById('lookup_charge_ugel_value').value = '';
            typeSelect.value = '';
            document.querySelectorAll('.interessado-selector-field').forEach(el => el.classList.add('d-none'));

            // Limpiar buscador de resoluciones
            const searchInput = document.getElementById('search_resolutions_input');
            if (searchInput) searchInput.value = '';

            const desdeInput = document.getElementById('search_desde');
            if (desdeInput) desdeInput.value = '';

            const hastaInput = document.getElementById('search_hasta');
            if (hastaInput) hastaInput.value = '';

            const resResults = document.getElementById('resolutions_results_container');
            if (resResults) resResults.classList.add('d-none');

            $('#resolutions_help_text').html('<span class="material-symbols-outlined fs-6 align-middle me-1">info</span>Filtre por fecha o escriba para buscar resoluciones sin cargo en el sistema.');
            this.clearAllSelectedResolutions();

            // Sincronizar selector de destinatarios por defecto
            const defaultRadio = document.getElementById('recipient_type_otros');
            if (defaultRadio) {
                defaultRadio.checked = true;
                defaultRadio.dispatchEvent(new Event('change'));
            }
        });

        // Toggle de visibilidad para AJAX Selects y Buscadores Manuales
        typeSelect.addEventListener('change', (e) => {
            const val = e.target.value;
            document.querySelectorAll('.interessado-selector-field').forEach(el => el.classList.add('d-none'));
            this.clearChargeRecipient();

            if (val === 'Persona Natural') {
                document.getElementById('container_natural_doc_type')?.classList.remove('d-none');
                document.getElementById('container_natural_person')?.classList.remove('d-none');
            }
            if (val === 'Persona Juridica') document.getElementById('container_legal_entity')?.classList.remove('d-none');
            if (val === 'Trabajador UGEL') document.getElementById('container_ugel_user')?.classList.remove('d-none');
        });

        // Toggle de destinatarios (Interesados vs Otros)
        const cargoParaRadios = document.querySelectorAll('input[name="cargo_para"]');
        cargoParaRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const val = e.target.value;
                const manualSection = document.getElementById('recipient_manual_section');
                const helpMsg = document.getElementById('recipient_help_message');
                
                if (val === 'interesados_resolucion') {
                    if (helpMsg) helpMsg.textContent = 'Se crearán cargos automáticamente para cada uno de los interesados registrados en las resoluciones seleccionadas.';
                    if (manualSection) manualSection.classList.add('d-none');
                    typeSelect.removeAttribute('required');
                } else {
                    if (helpMsg) helpMsg.textContent = 'Se creará un cargo para cada destinatario que agregue manualmente a continuación.';
                    if (manualSection) manualSection.classList.remove('d-none');
                    typeSelect.setAttribute('required', '');
                }
            });
        });

        // --- BÚSQUEDA MANUAL PERSONA NATURAL ---
        document.getElementById('btn_lookup_charge_natural')?.addEventListener('click', async () => {
            const docType = document.getElementById('charge_natural_doc_type').value;
            const value = document.getElementById('lookup_charge_natural_value').value.trim();
            const btn = document.getElementById('btn_lookup_charge_natural');
            const errorEl = document.getElementById('lookup_charge_natural_error');

            if (!value) return;
            errorEl.classList.add('d-none');
            this.clearChargeRecipient();
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                let data = null;
                if (docType === 'DNI') {
                    data = await ApiLookup.dni(value);
                } else {
                    const res = await fetch(route(`/search/natural-people/by-cedula/${encodeURIComponent(value)}`));
                    if (res.ok) {
                        const payload = await res.json();
                        data = payload.data;
                    }
                }

                if (data && data.nombres) {
                    this.confirmChargeRecipient({
                        id: data.id,
                        type: 'Persona Natural',
                        name: `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`,
                        identity: value
                    });
                } else {
                    errorEl.textContent = 'No se encontró a la persona. Verifique el número.';
                    errorEl.classList.remove('d-none');
                }
            } catch (e) {
                errorEl.textContent = 'Error en la conexión.';
                errorEl.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.textContent = 'BUSCAR';
            }
        });

        // --- BÚSQUEDA MANUAL PERSONA JURÍDICA ---
        document.getElementById('btn_lookup_charge_legal')?.addEventListener('click', async () => {
            const value = document.getElementById('lookup_charge_legal_value').value.trim();
            const btn = document.getElementById('btn_lookup_charge_legal');
            const errorEl = document.getElementById('lookup_charge_legal_error');

            if (!value) return;
            errorEl.classList.add('d-none');
            this.clearChargeRecipient();

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const data = await ApiLookup.ruc(value);
                if (data && data.razon_social) {
                    this.confirmChargeRecipient({
                        id: data.id,
                        type: 'Persona Juridica',
                        name: data.razon_social,
                        identity: value,
                        district: data.distrito || data.district || 'CUSCO'
                    });
                } else {
                    errorEl.textContent = 'RUC no encontrado.';
                    errorEl.classList.remove('d-none');
                }
            } catch (e) {
                errorEl.textContent = 'Error en la búsqueda.';
                errorEl.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.textContent = 'BUSCAR';
            }
        });

        // --- BÚSQUEDA MANUAL TRABAJADOR UGEL ---
        document.getElementById('btn_lookup_charge_ugel')?.addEventListener('click', async () => {
            const value = document.getElementById('lookup_charge_ugel_value').value.trim();
            const btn = document.getElementById('btn_lookup_charge_ugel');
            const errorEl = document.getElementById('lookup_charge_ugel_error');

            if (!value) return;
            errorEl.classList.add('d-none');
            this.clearChargeRecipient();

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch(route(`/search/users?q=${encodeURIComponent(value)}`));
                if (res.ok) {
                    const payload = await res.json();
                    if (payload.results && payload.results.length > 0) {
                        const user = payload.results[0]; // Tomar el primer resultado coincidente
                        const textParts = user.text.split(' - ');
                        const userName = textParts.slice(1).join(' - ') || user.text;

                        this.confirmChargeRecipient({
                            id: user.id,
                            type: 'Trabajador UGEL',
                            name: userName,
                            identity: user.dni || value
                        });
                    } else {
                        errorEl.textContent = 'No se encontró ningún trabajador.';
                        errorEl.classList.remove('d-none');
                    }
                } else {
                    errorEl.textContent = 'Error en la búsqueda.';
                    errorEl.classList.remove('d-none');
                }
            } catch (e) {
                errorEl.textContent = 'Error de conexión.';
                errorEl.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.textContent = 'BUSCAR';
            }
        });

        // Evento enter en el input de trabajador
        document.getElementById('lookup_charge_ugel_value')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btn_lookup_charge_ugel').click();
            }
        });

        // Inicializado dinámicamente en shown.bs.modal


        // 4. Búsqueda de Resoluciones con Filtros Avanzados
        const btnSearch = document.getElementById('btn_search_resolutions');
        const inputSearch = document.getElementById('search_resolutions_input');
        const resultsContainer = document.getElementById('resolutions_results_container');
        const listContainer = document.getElementById('resolutions_list');
        const resultsCount = document.getElementById('resolutions_results_count');

        if (btnSearch && inputSearch) {
            const performSearch = async () => {
                const query = inputSearch.value.trim();
                const desde = document.getElementById('search_desde')?.value || '';
                const hasta = document.getElementById('search_hasta')?.value || '';

                const hasActiveFilters = desde || hasta;
                
                if (!query && !hasActiveFilters) {
                    $('#resolutions_help_text').html('<span class="text-danger fw-bold"><span class="material-symbols-outlined fs-6 align-middle me-1">warning</span>Ingrese al menos un criterio o filtre por fecha para buscar.</span>');
                    return;
                }

                if (query && query.length < 2) {
                    $('#resolutions_help_text').html('<span class="text-danger fw-bold"><span class="material-symbols-outlined fs-6 align-middle me-1">warning</span>La búsqueda por texto requiere al menos 2 caracteres.</span>');
                    return;
                }

                btnSearch.disabled = true;
                btnSearch.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                
                try {
                    const params = new URLSearchParams();
                    if (query) params.append('q', query);
                    if (desde) params.append('desde', desde);
                    if (hasta) params.append('hasta', hasta);

                    const response = await fetch(route(`/search/pending-resolutions?${params.toString()}`));
                    const data = await response.json();
                    
                    listContainer.innerHTML = '';

                    if (data.results && data.results.length > 0) {
                        resultsCount.textContent = data.results.length;
                        data.results.forEach(res => {
                            const isAlreadyAdded = self.selectedResolutions.has(res.id.toString());
                            const btnHtml = isAlreadyAdded 
                                ? `<button type="button" class="btn btn-secondary btn-sm fw-bold" disabled>Añadida</button>`
                                : `<button type="button" class="btn btn-outline-success btn-sm fw-bold btn-add-res-to-list d-flex align-items-center gap-1" data-id="${res.id}" data-text="${res.text}">
                                       <span class="material-symbols-outlined fs-6">add</span> Añadir
                                   </button>`;

                            const item = document.createElement('div');
                            item.className = 'list-group-item d-flex gap-3 align-items-center justify-content-between py-2';
                            item.innerHTML = `
                                <div class="d-flex flex-column flex-grow-1">
                                    <span class="fw-bold text-dark small">${res.text.split(' - ')[0]}</span>
                                    <small class="text-muted text-truncate" style="max-width: 450px;">${res.text.split(' - ')[1] || ''}</small>
                                </div>
                                ${btnHtml}
                            `;
                            listContainer.appendChild(item);
                        });

                        // Asignar eventos de click a los botones de añadir recién creados
                        listContainer.querySelectorAll('.btn-add-res-to-list').forEach(btn => {
                            btn.addEventListener('click', (e) => {
                                const targetBtn = e.target.closest('.btn-add-res-to-list');
                                self.addResolutionToList(targetBtn.dataset.id, targetBtn.dataset.text);
                            });
                        });

                        resultsContainer.classList.remove('d-none');
                        $('#resolutions_help_text').html(`<span class="text-success"><span class="material-symbols-outlined fs-6 align-middle me-1">check_circle</span>Se encontraron ${data.results.length} resoluciones. Seleccione las que desea vincular.</span>`);
                    } else {
                        resultsContainer.classList.add('d-none');
                        $('#resolutions_help_text').html('<span class="text-warning fw-bold"><span class="material-symbols-outlined fs-6 align-middle me-1">search_off</span>No se encontraron resoluciones sin cargo con ese criterio.</span>');
                    }
                } catch (error) {
                    console.error('Error buscando resoluciones:', error);
                    $('#resolutions_help_text').html('<span class="text-danger"><span class="material-symbols-outlined fs-6 align-middle me-1">error</span>Error de conexión. Intente nuevamente.</span>');
                } finally {
                    btnSearch.disabled = false;
                    btnSearch.innerHTML = 'Buscar';
                }
            };

            btnSearch.addEventListener('click', performSearch);
            inputSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });
        }

        // --- LÓGICA DE AGREGACIÓN DE MÚLTIPLES DESTINATARIOS ---
        document.getElementById('btn_add_recipient_to_list')?.addEventListener('click', () => {
            if (!this.tempRecipient) return;

            // Evitar duplicados
            const exists = this.addedRecipients.some(r => r.identity === this.tempRecipient.identity);
            if (exists) {
                alert('Este destinatario ya ha sido añadido a la lista.');
                return;
            }

            this.addedRecipients.push(this.tempRecipient);
            this.renderAddedRecipients();
            this.clearChargeRecipient();

            // Limpiar inputs correspondientes
            const natInp = document.getElementById('lookup_charge_natural_value');
            if (natInp) natInp.value = '';

            const legInp = document.getElementById('lookup_charge_legal_value');
            if (legInp) legInp.value = '';

            const ugelInp = document.getElementById('lookup_charge_ugel_value');
            if (ugelInp) ugelInp.value = '';
        });

        // Validar en el submit del formulario createChargeForm
        const form = document.getElementById('createChargeForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                console.log('[Charges submit] Iniciando validación de submit para createChargeForm.');
                const resolutionsCount = this.selectedResolutions.size;
                console.log(`[Charges submit] Resoluciones vinculadas: ${resolutionsCount}`);

                // 1. Validar que se haya seleccionado al menos una resolución
                if (resolutionsCount === 0) {
                    console.warn('[Charges submit] Bloqueado: No hay resoluciones vinculadas.');
                    e.preventDefault();
                    alert('Debe vincular al menos una resolución antes de guardar.');
                    return;
                }

                // 2. Validar que haya al menos un destinatario si es "otros"
                const cargoPara = form.querySelector('input[name="cargo_para"]:checked')?.value || 'otros';
                console.log(`[Charges submit] Destinatario tipo: ${cargoPara}`);
                if (cargoPara === 'otros') {
                    const recipientsCount = this.addedRecipients.length;
                    console.log(`[Charges submit] Destinatarios en la lista manual: ${recipientsCount}`);
                    if (recipientsCount === 0) {
                        console.warn('[Charges submit] Bloqueado: Lista de destinatarios vacía.');
                        e.preventDefault();
                        alert('Debe añadir al menos un destinatario a la lista antes de guardar.');
                        return;
                    }
                }

                // 3. Validar asunto
                const asuntoInput = document.getElementById('asunto');
                const asuntoVal = asuntoInput ? asuntoInput.value.trim() : '';
                console.log(`[Charges submit] Asunto ingresado: "${asuntoVal}"`);
                if (!asuntoVal) {
                    console.warn('[Charges submit] Bloqueado: Asunto vacío.');
                    e.preventDefault();
                    alert('Debe ingresar el asunto (remitente) del cargo.');
                    if (asuntoInput) asuntoInput.focus();
                    return;
                }

                console.log('[Charges submit] Validación frontend exitosa. Enviando formulario al servidor.');
            });
        }
    },

    confirmChargeRecipient: function(data) {
        this.tempRecipient = data;
        document.getElementById('charge_recipient_name').textContent = data.name;
        document.getElementById('charge_recipient_id_text').textContent = `${data.type} | ID: ${data.identity}`;
        document.getElementById('charge_recipient_info').classList.remove('d-none');
    },

    clearChargeRecipient: function() {
        this.tempRecipient = null;
        const nameEl = document.getElementById('charge_recipient_name');
        const docEl = document.getElementById('charge_recipient_id_text');
        if (nameEl) nameEl.textContent = '';
        if (docEl) docEl.textContent = '';
        const infoEl = document.getElementById('charge_recipient_info');
        if (infoEl) infoEl.classList.add('d-none');
    },

    clearAllAddedRecipients: function() {
        this.addedRecipients = [];
        this.tempRecipient = null;
        const container = document.getElementById('charge_recipient_list_container');
        const hiddenContainer = document.getElementById('hidden_destinatarios_inputs');
        const wrapper = document.getElementById('charge_recipient_list_wrapper');
        if (container) container.innerHTML = '';
        if (hiddenContainer) hiddenContainer.innerHTML = '';
        if (wrapper) wrapper.classList.add('d-none');
    },

    renderAddedRecipients: function() {
        const container = document.getElementById('charge_recipient_list_container');
        const hiddenContainer = document.getElementById('hidden_destinatarios_inputs');
        const wrapper = document.getElementById('charge_recipient_list_wrapper');

        if (!container || !hiddenContainer || !wrapper) return;

        container.innerHTML = '';
        hiddenContainer.innerHTML = '';

        if (this.addedRecipients.length === 0) {
            wrapper.classList.add('d-none');
            return;
        }

        wrapper.classList.remove('d-none');

        this.addedRecipients.forEach((r, idx) => {
            const row = document.createElement('div');
            row.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
            row.innerHTML = `
                <div class="d-flex flex-column text-start">
                    <span class="fw-bold text-dark small">${r.name}</span>
                    <span class="text-muted small" style="font-size: 0.75rem;">${r.type} | ID: ${r.identity}</span>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm p-1 d-flex align-items-center btn-remove-recipient-manual" data-index="${idx}">
                    <span class="material-symbols-outlined fs-5">delete</span>
                </button>
            `;
            container.appendChild(row);

            const createHidden = (field, val) => {
                if (val === undefined || val === null) return;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `destinatarios[${idx}][${field}]`;
                input.value = val;
                hiddenContainer.appendChild(input);
            };

            createHidden('tipo', r.type);
            if (r.id) createHidden('id', r.id);

            if (r.type === 'Persona Natural') {
                createHidden('dni', r.identity);
                const parts = r.name.split(' ');
                createHidden('nombres', parts[0] || '');
                createHidden('apellido_paterno', parts[1] || '');
                createHidden('apellido_materno', parts.slice(2).join(' ') || '');
            } else if (r.type === 'Persona Juridica') {
                createHidden('ruc', r.identity);
                createHidden('razon_social', r.name);
                createHidden('district', r.district || 'CUSCO');
            } else if (r.type === 'Trabajador UGEL') {
                createHidden('assigned_to', r.id);
                createHidden('dni', r.identity);
                createHidden('nombres', r.name);
            }
        });

        container.querySelectorAll('.btn-remove-recipient-manual').forEach(btn => {
            btn.onclick = () => {
                const index = parseInt(btn.dataset.index);
                this.addedRecipients.splice(index, 1);
                this.renderAddedRecipients();
            };
        });
    },

    addResolutionToList: function(id, text) {
        id = id.toString();
        if (this.selectedResolutions.has(id)) {
            return;
        }
        this.selectedResolutions.add(id);

        const listSelectedContainer = document.getElementById('list_selected_resolutions');
        const hiddenInputsContainer = document.getElementById('hidden_resolutions_inputs');

        if (!listSelectedContainer || !hiddenInputsContainer) return;

        // Quitar la fila de "No hay resoluciones" si existe
        const emptyRow = listSelectedContainer.querySelector('.empty-resolutions-row');
        if (emptyRow) emptyRow.remove();

        const shortName = text.split(' - ')[0] || '';
        const details = text.split(' - ').slice(1).join(' - ') || '';

        // Crear la fila visual
        const row = document.createElement('tr');
        row.id = `selected_res_row_${id}`;
        row.innerHTML = `
            <td class="ps-3 py-2"><span class="badge bg-primary-subtle text-primary border border-primary-subtle">${shortName}</span></td>
            <td class="py-2"><div class="small text-dark text-truncate" style="max-width: 450px;" title="${details}">${details}</div></td>
            <td class="text-end pe-3 py-2">
                <button type="button" class="btn btn-outline-danger btn-sm p-1 d-flex align-items-center btn-remove-res" data-id="${id}">
                    <span class="material-symbols-outlined fs-5">delete</span>
                </button>
            </td>
        `;
        listSelectedContainer.appendChild(row);

        // Crear el input oculto
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'resolucion_ids[]';
        input.value = id;
        input.id = `hidden_res_input_${id}`;
        hiddenInputsContainer.appendChild(input);
        
        // Asociar evento de eliminación
        row.querySelector('.btn-remove-res').addEventListener('click', () => {
            this.removeResolutionFromList(id);
        });

        // Actualizar el estado del botón en la lista de resultados de búsqueda si existe
        const btnInResults = document.querySelector(`.btn-add-res-to-list[data-id="${id}"]`);
        if (btnInResults) {
            btnInResults.disabled = true;
            btnInResults.className = 'btn btn-secondary btn-sm fw-bold';
            btnInResults.textContent = 'Añadida';
        }
    },

    removeResolutionFromList: function(id) {
        id = id.toString();
        this.selectedResolutions.delete(id);
        document.getElementById(`selected_res_row_${id}`)?.remove();
        document.getElementById(`hidden_res_input_${id}`)?.remove();

        if (this.selectedResolutions.size === 0) {
            this.renderEmptyResolutionsRow();
        }

        // Rehabilitar el botón en la lista de resultados de búsqueda si existe
        const btnInResults = document.querySelector(`.btn-add-res-to-list[data-id="${id}"]`);
        if (btnInResults) {
            btnInResults.disabled = false;
            btnInResults.className = 'btn btn-outline-success btn-sm fw-bold btn-add-res-to-list d-flex align-items-center gap-1';
            btnInResults.innerHTML = '<span class="material-symbols-outlined fs-6">add</span> Añadir';
            
            // Re-vincular evento de click
            btnInResults.onclick = (e) => {
                const targetBtn = e.target.closest('.btn-add-res-to-list');
                this.addResolutionToList(targetBtn.dataset.id, targetBtn.dataset.text);
            };
        }
    },

    renderEmptyResolutionsRow: function() {
        const listSelectedContainer = document.getElementById('list_selected_resolutions');
        if (listSelectedContainer) {
            listSelectedContainer.innerHTML = `
                <tr class="empty-resolutions-row">
                    <td colspan="3" class="text-center py-4 text-muted small italic">
                        <span class="material-symbols-outlined fs-2 d-block mb-1">link_off</span>
                        No hay resoluciones añadidas a este cargo.
                    </td>
                </tr>
            `;
        }
    },

    clearAllSelectedResolutions: function() {
        this.selectedResolutions.clear();
        const listSelectedContainer = document.getElementById('list_selected_resolutions');
        const hiddenInputsContainer = document.getElementById('hidden_resolutions_inputs');
        if (listSelectedContainer) listSelectedContainer.innerHTML = '';
        if (hiddenInputsContainer) hiddenInputsContainer.innerHTML = '';
        this.renderEmptyResolutionsRow();
    },

    formatResolutionResult: function(res) {
        if (!res.id) return res.text;
        
        return `
            <div class="d-flex align-items-center">
                <span class="material-symbols-outlined text-muted me-2" style="font-size: 1.1rem;">description</span>
                <span class="text-dark">${res.text}</span>
            </div>
        `;
    },

    formatResolutionSelection: function(res) {
        if (!res.id) return res.text;
        return `<span class="d-flex align-items-center fw-medium"><span class="material-symbols-outlined me-1" style="font-size: 1rem;">description</span>${res.text}</span>`;
    },

    /**
     * Lógica para el formulario MANUAL (Módulo de Cargos)
     */
    setupManualLogic: function() {
        const typeSelect = document.getElementById('tipo_interesado_manual');
        if (!typeSelect) return;

        const toggle = () => {
            const val = typeSelect.value;
            document.querySelector('.persona-natural-fields')?.classList.toggle('d-none', val !== 'Persona Natural');
            document.querySelector('.persona-juridica-fields')?.classList.toggle('d-none', val !== 'Persona Juridica');
            document.querySelector('.assigned-user-field')?.classList.toggle('d-none', val !== 'Trabajador UGEL');
            
            // Limpiar detalles internos al cambiar
            document.querySelectorAll('.natural-details, .entity-details').forEach(el => el.classList.add('d-none'));
        };

        typeSelect.addEventListener('change', toggle);
        toggle();

        if ($.fn.select2) {
            $('.select2-user').select2({ theme: 'bootstrap-5', dropdownParent: $('#createChargeModal'), width: '100%' });
        }
    },

    initStaticSelect2: function() {
        if (!$.fn.select2) return;
        $('.select2-user-edit, .select2-resolutions-edit').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#editChargeModal'),
            width: '100%'
        });

        // Toggle para edición
        const editSelect = document.getElementById('edit_tipo_interesado');
        if (editSelect) {
            const toggleEdit = () => {
                const val = editSelect.value;
                document.querySelector('.persona-natural-fields-edit')?.classList.toggle('d-none', val !== 'Persona Natural');
                document.querySelector('.persona-juridica-fields-edit')?.classList.toggle('d-none', val !== 'Persona Juridica');
                document.querySelector('.assigned-user-field-edit')?.classList.toggle('d-none', val !== 'Trabajador UGEL');
            };
            editSelect.addEventListener('change', toggleEdit);
            toggleEdit();
        }
    }
};
