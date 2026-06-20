import { route } from '../common/url-helper';
import { ApiLookup } from '../common/api-lookup';

export const FormModule = {
    init: function() {
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
            document.getElementById('lookup_charge_natural_value').value = '';
            document.getElementById('lookup_charge_legal_value').value = '';
            typeSelect.value = '';
            document.querySelectorAll('.interessado-selector-field').forEach(el => el.classList.add('d-none'));
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
                        identity: value
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

        // Inicializar Select2 AJAX (Solo para Trabajadores UGEL)
        if ($.fn.select2) {
            const modal = $('#createChargeModal');

            $('.select2-ajax-users').select2({
                theme: 'bootstrap-5', dropdownParent: modal,
                width: '100%',
                ajax: {
                    url: route('/search/users'), dataType: 'json', delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                },
                minimumInputLength: 2, placeholder: 'Buscar trabajador...'
            });
        }

        // 4. Búsqueda de Resoluciones (Checkboxes)
        const btnSearch = document.getElementById('btn_search_resolutions');
        const inputSearch = document.getElementById('search_resolutions_input');
        const resultsContainer = document.getElementById('resolutions_results_container');
        const listContainer = document.getElementById('resolutions_list');
        const resultsCount = document.getElementById('resolutions_results_count');
        const selectAllCheckbox = document.getElementById('select_all_resolutions');

        if (btnSearch && inputSearch) {
            const performSearch = async () => {
                const query = inputSearch.value.trim();
                if (query.length < 2) {
                    $('#resolutions_help_text').html('<span class="text-danger fw-bold"><span class="material-symbols-outlined fs-6 align-middle me-1">warning</span>Ingrese al menos 2 caracteres para buscar.</span>');
                    return;
                }

                btnSearch.disabled = true;
                btnSearch.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                
                try {
                    const response = await fetch(route(`/search/pending-resolutions?q=${encodeURIComponent(query)}`));
                    const data = await response.json();
                    
                    listContainer.innerHTML = '';
                    selectAllCheckbox.checked = false;

                    if (data.results && data.results.length > 0) {
                        resultsCount.textContent = data.results.length;
                        data.results.forEach(res => {
                            // Check if this checkbox already exists in the DOM to preserve selection state if we were doing a real complex state manager, 
                            // but here we just render fresh checkboxes based on search.
                            
                            const item = document.createElement('label');
                            item.className = 'list-group-item list-group-item-action d-flex gap-3 align-items-center cursor-pointer';
                            item.innerHTML = `
                                <input class="form-check-input flex-shrink-0 resolution-checkbox" type="checkbox" name="resolucion_ids[]" value="${res.id}" style="font-size: 1.2em;">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">${res.text.split(' - ')[0]}</span>
                                    <small class="text-muted text-truncate" style="max-width: 400px;">${res.text.split(' - ')[1] || ''}</small>
                                </div>
                            `;
                            listContainer.appendChild(item);
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

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', (e) => {
                    const checkboxes = listContainer.querySelectorAll('.resolution-checkbox');
                    checkboxes.forEach(cb => cb.checked = e.target.checked);
                });
            }
        }
    },

    confirmChargeRecipient: function(data) {
        document.getElementById('charge_recipient_name').textContent = data.name;
        document.getElementById('charge_recipient_id_text').textContent = `${data.type} | ID: ${data.identity}`;
        
        if (data.type === 'Persona Natural') {
            document.getElementById('hidden_charge_natural_id').value = data.id;
            document.getElementById('hidden_charge_legal_id').value = '';
            document.getElementById('lookup_charge_natural_value').value = '';
        } else {
            document.getElementById('hidden_charge_legal_id').value = data.id;
            document.getElementById('hidden_charge_natural_id').value = '';
            document.getElementById('lookup_charge_legal_value').value = '';
        }

        document.getElementById('charge_recipient_info').classList.remove('d-none');
    },

    clearChargeRecipient: function() {
        document.getElementById('charge_recipient_name').textContent = '';
        document.getElementById('charge_recipient_id_text').textContent = '';
        document.getElementById('hidden_charge_natural_id').value = '';
        document.getElementById('hidden_charge_legal_id').value = '';
        document.getElementById('charge_recipient_info').classList.add('d-none');
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
