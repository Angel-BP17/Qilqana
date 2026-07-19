import { ImportHelper } from '../common/import-helper';
import { ApiLookup } from '../common/api-lookup';
import { route } from '../common/url-helper';

/**
 * Clase para gestionar la lista de interesados en los formularios.
 */
class InteresadosManager {
    constructor(prefix) {
        this.prefix = prefix; // 'create' o 'edit'
        this.interesados = [];
        this.tempResult = null; // Resultado temporal de la búsqueda manual
    }

    init() {
        const p = this.prefix === 'create' ? '' : 'edit_';
        const typeSelector = document.getElementById(`${p}selector_interesado_type`);
        if (!typeSelector) return;

        typeSelector.addEventListener('change', (e) => this.toggleSearchWrappers(e.target.value));

        // Botones de Búsqueda Manual
        document.getElementById(`${p}btn_lookup_natural`)?.addEventListener('click', () => this.handleNaturalLookup());
        document.getElementById(`${p}btn_lookup_legal`)?.addEventListener('click', () => this.handleLegalLookup());

        // Botones de "Añadir a la lista"
        document.getElementById(`${p}btn_add_natural_list`)?.addEventListener('click', () => this.confirmAddResult('Persona Natural'));
        document.getElementById(`${p}btn_add_legal_list`)?.addEventListener('click', () => this.confirmAddResult('Persona Juridica'));
        document.getElementById(`${p}btn_add_ugel_list`)?.addEventListener('click', () => this.handleWorkerAdd());

        // Botones de guardado manual (Cuando no se encuentra en API/DB)
        document.getElementById(`${p}btn_save_manual_natural`)?.addEventListener('click', () => this.handleManualSaveAdd('Persona Natural'));
        document.getElementById(`${p}btn_save_manual_legal`)?.addEventListener('click', () => this.handleManualSaveAdd('Persona Juridica'));
    }

    toggleSearchWrappers(val) {
        const p = this.prefix === 'create' ? '' : 'edit_';
        document.querySelectorAll(`.interessado-form-block`).forEach(el => el.classList.add('d-none'));
        
        if (val === 'Persona Natural') document.getElementById(`${p}wrapper_natural_search`)?.classList.remove('d-none');
        if (val === 'Persona Juridica') document.getElementById(`${p}wrapper_legal_search`)?.classList.remove('d-none');
        if (val === 'Trabajador UGEL') document.getElementById(`${p}wrapper_ugel_search`)?.classList.remove('d-none');
        
        this.clearTempResults();
    }

    async handleNaturalLookup() {
        const p = this.prefix === 'create' ? '' : 'edit_';
        const type = document.getElementById(`${p}lookup_natural_doc_type`).value;
        const value = document.getElementById(`${p}lookup_natural_value`).value.trim();
        const btn = document.getElementById(`${p}btn_lookup_natural`);
        const errorEl = document.getElementById(`${p}lookup_natural_error`);
        const resultContainer = document.getElementById(`${p}result_natural_details`);
        const resultText = document.getElementById(`${p}text_natural_result`);
        const manualWrapper = document.getElementById(`${p}wrapper_natural_manual`);

        if (!value) return;
        this.clearTempResults();
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            let data = null;
            if (type === 'DNI') {
                data = await ApiLookup.dni(value);
            } else {
                const res = await fetch(route(`/search/natural-people/by-cedula/${encodeURIComponent(value)}`), {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const payload = await res.json();
                    data = payload.data;
                }
            }

            if (data && data.nombres) {
                this.tempResult = {
                    id: data.id || null, // null significa que debe crearse en el server
                    type: 'Persona Natural',
                    text: `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`,
                    identity: value,
                    dni: type === 'DNI' ? value : null,
                    cedula: type === 'CEDULA' ? value : null,
                    nombres: data.nombres,
                    apellido_paterno: data.apellido_paterno,
                    apellido_materno: data.apellido_materno
                };
                resultText.textContent = this.tempResult.text;
                resultContainer.classList.remove('d-none');
            } else {
                // Mostrar formulario manual
                manualWrapper.classList.remove('d-none');
            }
        } catch (e) {
            console.error(e);
            errorEl.textContent = 'Error en la conexión.';
            errorEl.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.textContent = 'BUSCAR';
        }
    }

    async handleLegalLookup() {
        const p = this.prefix === 'create' ? '' : 'edit_';
        const value = document.getElementById(`${p}lookup_legal_value`).value.trim();
        const btn = document.getElementById(`${p}btn_lookup_legal`);
        const errorEl = document.getElementById(`${p}lookup_legal_error`);
        const resultContainer = document.getElementById(`${p}result_legal_details`);
        const resultText = document.getElementById(`${p}text_legal_result`);
        const manualWrapper = document.getElementById(`${p}wrapper_legal_manual`);

        if (!value) return;
        this.clearTempResults();

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const data = await ApiLookup.ruc(value);
            if (data && data.razon_social) {
                this.tempResult = {
                    id: data.id || null,
                    type: 'Persona Juridica',
                    text: data.razon_social,
                    identity: value,
                    ruc: value,
                    razon_social: data.razon_social,
                    district: data.district || ''
                };
                resultText.textContent = this.tempResult.text;
                resultContainer.classList.remove('d-none');
            } else {
                manualWrapper.classList.remove('d-none');
            }
        } catch (e) {
            errorEl.textContent = 'Error en la búsqueda.';
            errorEl.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.textContent = 'BUSCAR';
        }
    }

    handleManualSaveAdd(type) {
        const p = this.prefix === 'create' ? '' : 'edit_';
        if (type === 'Persona Natural') {
            const val = document.getElementById(`${p}lookup_natural_value`).value.trim();
            const docType = document.getElementById(`${p}lookup_natural_doc_type`).value;
            const noms = document.getElementById(`manual_natural_nombres`).value.trim();
            const pat = document.getElementById(`manual_natural_paterno`).value.trim();
            const mat = document.getElementById(`manual_natural_materno`).value.trim();

            if (!noms || !pat) return alert('Nombres y Apellido Paterno son obligatorios.');

            this.addInteresado({
                id: null,
                type: 'Persona Natural',
                text: `${noms} ${pat} ${mat}`,
                identity: val,
                dni: docType === 'DNI' ? val : null,
                cedula: docType === 'CEDULA' ? val : null,
                nombres: noms,
                apellido_paterno: pat,
                apellido_materno: mat
            });
        } else {
            const ruc = document.getElementById(`${p}lookup_legal_value`).value.trim();
            const razon = document.getElementById(`manual_legal_razon_social`).value.trim();
            const dist = document.getElementById(`manual_legal_district`).value.trim();

            if (!razon) return alert('La Razón Social es obligatoria.');

            this.addInteresado({
                id: null,
                type: 'Persona Juridica',
                text: razon,
                identity: ruc,
                ruc: ruc,
                razon_social: razon,
                district: dist
            });
        }
        this.clearTempResults();
        // Limpiar campos de entrada
        if (type === 'Persona Natural') {
            document.getElementById(`${p}lookup_natural_value`).value = '';
            document.getElementById(`manual_natural_nombres`).value = '';
            document.getElementById(`manual_natural_paterno`).value = '';
            document.getElementById(`manual_natural_materno`).value = '';
        } else {
            document.getElementById(`${p}lookup_legal_value`).value = '';
            document.getElementById(`manual_legal_razon_social`).value = '';
            document.getElementById(`manual_legal_district`).value = '';
        }
    }

    confirmAddResult(type) {
        if (this.tempResult && this.tempResult.type === type) {
            this.addInteresado(this.tempResult);
            this.clearTempResults();
            const p = this.prefix === 'create' ? '' : 'edit_';
            const inputId = type === 'Persona Natural' ? `${p}lookup_natural_value` : `${p}lookup_legal_value`;
            document.getElementById(inputId).value = '';
        }
    }

    handleWorkerAdd() {
        const p = this.prefix === 'create' ? '' : 'edit_';
        const selectEl = document.getElementById(`${p}search_ugel_interesado`);
        if (!selectEl || !selectEl.tomselect) return;

        const val = selectEl.tomselect.getValue();
        if (val) {
            const data = selectEl.tomselect.options[val];
            if (data) {
                this.addInteresado({
                    id: data.id,
                    type: 'Trabajador UGEL',
                    text: data.text.split(' - ')[1] || data.text,
                    identity: data.dni || '---'
                });
                selectEl.tomselect.clear();
            }
        }
    }

    initWorkerSelect2() {
        const p = this.prefix === 'create' ? '' : 'edit_';
        const modalId = this.prefix === 'create' ? 'createResolutionModal' : 'editResolutionModal';
        const modalElement = document.getElementById(modalId);
        if (!modalElement) return;

        const selectEl = document.getElementById(`${p}search_ugel_interesado`);
        if (!selectEl) return;

        if (selectEl.tomselect) {
            selectEl.tomselect.clear();
            return;
        }

        if (window.TomSelect) {
            new TomSelect(`#${p}search_ugel_interesado`, {
                valueField: 'id',
                labelField: 'text',
                searchField: 'text',
                placeholder: 'Buscar trabajador...',
                load: function(query, callback) {
                    if (!query.length) return callback();
                    fetch(route(`/search/users?q=${encodeURIComponent(query)}`))
                        .then(response => response.json())
                        .then(json => {
                            callback(json.results);
                        }).catch(() => {
                            callback();
                        });
                },
                render: {
                    option: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    },
                    item: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    }
                }
            });
        }
    }

    clearTempResults() {
        const p = this.prefix === 'create' ? '' : 'edit_';
        this.tempResult = null;
        ['natural', 'legal'].forEach(t => {
            document.getElementById(`${p}result_${t}_details`)?.classList.add('d-none');
            document.getElementById(`${p}lookup_${t}_error`)?.classList.add('d-none');
            document.getElementById(`${p}wrapper_${t}_manual`)?.classList.add('d-none');
        });
    }

    addInteresado(interesado) {
        if (this.interesados.find(i => i.identity == interesado.identity && i.type === interesado.type && i.identity !== '---')) return;
        this.interesados.push(interesado);
        this.render();
    }

    removeInteresado(id, identity, type) {
        this.interesados = this.interesados.filter(i => !(i.id == id && i.identity == identity && i.type === type));
        this.render();
    }

    clear() {
        this.interesados = [];
        this.render();
    }

    render() {
        const p = this.prefix === 'create' ? '' : 'edit_';
        const tbody = document.getElementById(`${p}list_selected_interesados`);
        const hiddenContainer = document.getElementById(`${p}hidden_interesados_inputs`);
        
        if (!tbody || !hiddenContainer) return;

        tbody.innerHTML = '';
        hiddenContainer.innerHTML = '';

        if (this.interesados.length === 0) {
            tbody.innerHTML = `
                <tr class="empty-interesados-row">
                    <td colspan="4" class="text-center py-4 text-muted small italic">
                        <span class="material-symbols-outlined fs-2 d-block mb-1">person_off</span>
                        No hay interesados añadidos a esta resolución.
                    </td>
                </tr>`;
            return;
        }

        this.interesados.forEach((i, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3">
                    <span class="badge ${i.type === 'Trabajador UGEL' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary'} border small">
                        ${i.type}
                    </span>
                </td>
                <td><div class="fw-bold small text-uppercase">${i.text}</div></td>
                <td><code class="small text-dark fw-bold">${i.identity}</code></td>
                <td class="text-end pe-3">
                    <button type="button" class="btn btn-link btn-sm text-danger p-0 btn-remove-interesado" 
                        data-id="${i.id}" data-identity="${i.identity}" data-type="${i.type}">
                        <span class="material-symbols-outlined fs-5">delete_forever</span>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

            // Crear inputs ocultos
            const createHidden = (name, val) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `interesados[${index}][${name}]`;
                input.value = val || '';
                hiddenContainer.appendChild(input);
            };

            createHidden('id', i.id);
            createHidden('type', i.type);
            
            if (!i.id) {
                if (i.type === 'Persona Natural') {
                    createHidden('dni', i.dni);
                    createHidden('cedula', i.cedula);
                    createHidden('nombres', i.nombres);
                    createHidden('apellido_paterno', i.apellido_paterno);
                    createHidden('apellido_materno', i.apellido_materno);
                } else if (i.type === 'Persona Juridica') {
                    createHidden('ruc', i.ruc);
                    createHidden('razon_social', i.razon_social);
                    createHidden('district', i.district);
                }
            }
        });

        tbody.querySelectorAll('.btn-remove-interesado').forEach(btn => {
            btn.onclick = () => this.removeInteresado(btn.dataset.id, btn.dataset.identity, btn.dataset.type);
        });
    }
}

export const ResolucionsManagement = {
    init: function() {
        this.setupModals();
        this.setupDetailsModal();
        this.setupEditModal();
        this.setupDeleteModal();
        this.setupSelectInteresadoModal();
        this.setupImport();
        this.setupFileLabel();
        this.setupSubfilter();
        this.setupFormSubmissionFeedback();
        this.setupInteresadoToggles();
        this.setupAsuntoTypesDynamic();
        this.setupWorkConfirmation();
        this.setupModalAccessibility();
    },

    setupModals: function() {
        const self = this;
        const createModalEl = document.getElementById('createResolutionModal');
        const editModalEl = document.getElementById('editResolutionModal');
        
        if (createModalEl) {
            this.createModal = new bootstrap.Modal(createModalEl, { focus: false });
        }
        if (editModalEl) {
            this.editModal = new bootstrap.Modal(editModalEl, { focus: false });
        }

        document.addEventListener('focusin', (e) => {
            if (e.target.closest(".ts-wrapper, .ts-control, .ts-dropdown") !== null) {
                e.stopImmediatePropagation();
            }
        });

        document.addEventListener('shown.bs.modal', function (e) {
            if (e.target.id === 'createResolutionModal' || e.target.id === 'editResolutionModal') {
                const prefix = e.target.id === 'createResolutionModal' ? 'create' : 'edit';
                const manager = prefix === 'create' ? self.createInteresadosManager : self.editInteresadosManager;
                if (manager) {
                    manager.initWorkerSelect2();
                }
            }
        });
    },

    setupAsuntoTypesDynamic: function() {
        const setupDynamic = (typeSelectId, asuntoSelectId) => {
            const typeSelect = document.getElementById(typeSelectId);
            const asuntoSelect = document.getElementById(asuntoSelectId);
            if (!typeSelect || !asuntoSelect) return;

            const loadAsuntos = async (resolutionTypeId, selectedAsuntoId = null) => {
                if (!selectedAsuntoId && asuntoSelect.dataset.selected) {
                    selectedAsuntoId = asuntoSelect.dataset.selected;
                }

                asuntoSelect.innerHTML = '<option value="">Cargando asuntos...</option>';
                asuntoSelect.disabled = true;

                if (!resolutionTypeId) {
                    asuntoSelect.innerHTML = '<option value="">Seleccione tipo de resolución primero...</option>';
                    return;
                }

                try {
                    const response = await fetch(route(`/search/asuntos-by-resolution-type/${resolutionTypeId}`));
                    const data = await response.json();

                    asuntoSelect.innerHTML = '<option value="">Seleccione tipo de asunto...</option>';
                    if (data.results && data.results.length > 0) {
                        data.results.forEach(asunto => {
                            const option = new Option(asunto.text, asunto.id);
                            if (selectedAsuntoId && selectedAsuntoId == asunto.id) {
                                option.selected = true;
                            }
                            asuntoSelect.add(option);
                        });
                        asuntoSelect.disabled = false;
                    } else {
                        asuntoSelect.innerHTML = '<option value="">No hay asuntos para este tipo...</option>';
                    }
                } catch (error) {
                    console.error('Error cargando tipos de asunto:', error);
                    asuntoSelect.innerHTML = '<option value="">Error cargando asuntos.</option>';
                }
            };

            typeSelect.addEventListener('change', (e) => {
                if (e.isTrusted) {
                    asuntoSelect.dataset.selected = ''; // Limpiar seleccionado ante cambios manuales
                }
                loadAsuntos(e.target.value);
            });

            if (typeSelect.value) {
                loadAsuntos(typeSelect.value, asuntoSelect.dataset.selected);
            }
        };

        setupDynamic('create_resolution_type', 'create_asunto_type');
        setupDynamic('edit_resolution_type', 'edit_asunto_type');
        setupDynamic('filter_resolution_type', 'filter_asunto_type');
    },

    setupInteresadoToggles: function() {
        const createManager = new InteresadosManager('create');
        const editManager = new InteresadosManager('edit');

        this.createInteresadosManager = createManager;
        this.editInteresadosManager = editManager;

        createManager.init();
        editManager.init();
    },

    setupDetailsModal: function() {
        const modalEl = document.getElementById('detailModal');
        if (!modalEl) return;

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-view-resolution');
            if (!btn) return;

            const fields = ['rd', 'fecha', 'dni', 'cedula', 'ruc', 'razon_social', 'nombres_apellidos', 'asunto', 'procedencia', 'periodo'];
            fields.forEach(f => {
                const el = document.getElementById(`modal-${f}`);
                if (el) el.textContent = btn.dataset[f] || '-';
            });

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupEditModal: function() {
        const self = this;
        const modalEl = document.getElementById('editResolutionModal');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        const form = document.getElementById('editResolutionForm');

        // Limpiar archivo al abrir modal de edición y de creación
        modalEl.addEventListener('show.bs.modal', () => {
            const fileInput = document.getElementById('edit_resolution_file');
            if (fileInput) fileInput.value = '';

            // Reiniciar estado de eliminación de PDF
            const deleteInput = document.getElementById('edit_delete_document_input');
            if (deleteInput) deleteInput.value = '0';

            const deletedFeedback = document.getElementById('edit_resolution_pdf_deleted_feedback');
            if (deletedFeedback) deletedFeedback.classList.add('d-none');
        });

        document.getElementById('createResolutionModal')?.addEventListener('show.bs.modal', () => {
            const fileInput = document.getElementById('create_resolution_file');
            if (fileInput) fileInput.value = '';
        });

        // Escuchar selección de archivo nuevo en edición para anular marcado de eliminación
        const fileInputEdit = document.getElementById('edit_resolution_file');
        if (fileInputEdit) {
            fileInputEdit.addEventListener('change', () => {
                const deleteInput = document.getElementById('edit_delete_document_input');
                const deletedFeedback = document.getElementById('edit_resolution_pdf_deleted_feedback');
                if (deleteInput) deleteInput.value = '0';
                if (deletedFeedback) deletedFeedback.classList.add('d-none');
            });
        }

        document.querySelectorAll('.btn-edit-resolution').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                
                const hiddenEditId = document.getElementById('edit_resolucion_id_hidden');
                if (hiddenEditId) {
                    hiddenEditId.value = btn.dataset.id || '';
                    console.log(`[Resolucions edit] ID de resolución cargado en input oculto: ${hiddenEditId.value}`);
                }
                
                ['rd', 'fecha', 'procedencia', 'asunto'].forEach(f => {
                    const el = document.getElementById(`edit_resolution_${f}`);
                    if (el) el.value = btn.dataset[f] || '';
                });

                // Manejar botón de visualizar y eliminar PDF en la edición
                const pdfContainer = document.getElementById('edit_resolution_pdf_container');
                if (pdfContainer) {
                    if (btn.dataset.documentUrl) {
                        pdfContainer.innerHTML = `
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <a href="${btn.dataset.documentUrl}" target="_blank" class="btn btn-outline-primary btn-sm fw-bold d-inline-flex align-items-center">
                                    <span class="material-symbols-outlined fs-5 me-1">picture_as_pdf</span> Ver PDF Actual
                                </a>
                                <button type="button" id="edit_btn_delete_pdf" class="btn btn-outline-danger btn-sm fw-bold d-inline-flex align-items-center">
                                    <span class="material-symbols-outlined fs-5 me-1">delete</span> Eliminar PDF
                                </button>
                            </div>
                        `;
                        pdfContainer.classList.remove('d-none');

                        // Configurar doble confirmación del botón de eliminar PDF
                        const btnDeletePdf = document.getElementById('edit_btn_delete_pdf');
                        const deleteInput = document.getElementById('edit_delete_document_input');
                        const deletedFeedback = document.getElementById('edit_resolution_pdf_deleted_feedback');

                        if (btnDeletePdf && deleteInput && deletedFeedback) {
                            let clickCount = 0;
                            btnDeletePdf.addEventListener('click', (e) => {
                                e.preventDefault();
                                clickCount++;
                                if (clickCount === 1) {
                                    btnDeletePdf.className = "btn btn-danger btn-sm fw-bold d-inline-flex align-items-center";
                                    btnDeletePdf.innerHTML = `<span class="material-symbols-outlined fs-5 me-1">warning</span> ¿Confirmar eliminación?`;
                                } else if (clickCount === 2) {
                                    deleteInput.value = '1';
                                    pdfContainer.classList.add('d-none');
                                    deletedFeedback.classList.remove('d-none');
                                }
                            });
                        }
                    } else {
                        pdfContainer.innerHTML = '';
                        pdfContainer.classList.add('d-none');
                    }
                }

                const resTypeSelect = document.getElementById('edit_resolution_type');
                if (resTypeSelect) {
                    resTypeSelect.value = btn.dataset.resolucion_type_id || '';
                    const asuntoSelect = document.getElementById('edit_asunto_type');
                    if (asuntoSelect) {
                        asuntoSelect.dataset.selected = btn.dataset.asunto_type_id || '';
                    }
                    resTypeSelect.dispatchEvent(new Event('change'));
                }

                const levelModalitySelect = document.getElementById('edit_level_modality');
                if (levelModalitySelect) {
                    levelModalitySelect.value = btn.dataset.level_modality_id || '';
                }

                if (self.editInteresadosManager) {
                    self.editInteresadosManager.clear();
                    if (btn.dataset.interesados) {
                        try {
                            const interesados = JSON.parse(btn.dataset.interesados);
                            interesados.forEach(i => self.editInteresadosManager.addInteresado(i));
                        } catch (e) {
                            console.error('Error parseando interesados:', e);
                        }
                    }
                }

                modal.show();
            };
        });
    },

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteResolutionModal');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        const form = document.getElementById('deleteResolutionForm');

        document.querySelectorAll('.btn-delete-resolution').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('delete_resolution_reason').value = '';
                modal.show();
            };
        });
    },

    setupImport: function() {
        ImportHelper.setup('1', 'importExcelButton');
    },

    setupFileLabel: function() {
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('custom-file-input')) {
                const fileName = e.target.value.split('\\').pop();
                const label = e.target.nextElementSibling;
                if (label && label.classList.contains('custom-file-label')) {
                    label.classList.add('selected');
                    label.innerHTML = fileName;
                }
            }
        });
    },

    setupFormSubmissionFeedback: function() {
        const self = this;
        const forms = [
            { id: 'createResolutionForm', manager: () => self.createInteresadosManager, name: 'Creación de Resolución' },
            { id: 'editResolutionForm', manager: () => self.editInteresadosManager, name: 'Edición de Resolución' }
        ];

        forms.forEach(item => {
            const form = document.getElementById(item.id);
            if (!form) return;
            form.addEventListener('submit', function(e) {
                console.log(`[Resolucions submit] Iniciando envío de formulario: ${item.name}`);

                console.log(`[Resolucions submit] Validación aprobada. Deshabilitando botón de submit en el siguiente tick.`);
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.classList.add('btn-loading');
                    setTimeout(() => {
                        btn.disabled = true;
                        console.log(`[Resolucions submit] Botón de submit deshabilitado.`);
                    }, 0);
                }
            });
        });

        // Formulario de eliminación (no requiere interesados)
        const deleteForm = document.getElementById('deleteResolutionForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function() {
                console.log('[Resolucions submit] Enviando formulario de eliminación de resolución.');
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.classList.add('btn-loading');
                    setTimeout(() => {
                        btn.disabled = true;
                    }, 0);
                }
            });
        }
    },

    setupSubfilter: function() {
        const input = document.getElementById('subfilter-input');
        if (!input) return;

        let debounceTimer = null;

        const getFilterParams = () => {
            const params = new URLSearchParams();
            
            // Filtros de la cabecera
            const headerForm = document.getElementById('resolutionSearchForm');
            if (headerForm) {
                new FormData(headerForm).forEach((value, key) => {
                    if (value) params.set(key, value);
                });
            }
            
            // Filtros del subfiltro (búsqueda general)
            const subfilterForm = document.getElementById('subfilterForm');
            if (subfilterForm) {
                new FormData(subfilterForm).forEach((value, key) => {
                    if (value) params.set(key, value);
                });
            }
            
            // Filtros avanzados del modal
            const modalFilterForm = document.getElementById('modalFilterForm');
            if (modalFilterForm) {
                new FormData(modalFilterForm).forEach((value, key) => {
                    if (value) params.set(key, value);
                });
            }
            
            return params;
        };

        const performAjaxSearch = (url) => {
            const tableSection = document.querySelector('section.table-responsive');
            const mobileSection = document.querySelector('section.d-md-none');
            const paginationContainer = document.getElementById('pagination-container');
            
            if (tableSection) tableSection.style.opacity = '0.5';
            if (mobileSection) mobileSection.style.opacity = '0.5';
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newTable = doc.querySelector('section.table-responsive');
                if (tableSection && newTable) {
                    tableSection.innerHTML = newTable.innerHTML;
                    tableSection.style.opacity = '1';
                }
                
                const newMobile = doc.querySelector('section.d-md-none');
                if (mobileSection && newMobile) {
                    mobileSection.innerHTML = newMobile.innerHTML;
                    mobileSection.style.opacity = '1';
                }
                
                const newPagination = doc.getElementById('pagination-container');
                if (paginationContainer) {
                    if (newPagination) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                        paginationContainer.classList.remove('d-none');
                    } else {
                        paginationContainer.innerHTML = '';
                        paginationContainer.classList.add('d-none');
                    }
                }

                // Actualizar el botón de limpiar filtros en la cabecera
                const headerButtons = document.querySelector('#resolutionSearchForm .d-flex.gap-2');
                const newHeaderButtons = doc.querySelector('#resolutionSearchForm .d-flex.gap-2');
                if (headerButtons && newHeaderButtons) {
                    headerButtons.innerHTML = newHeaderButtons.innerHTML;
                }
                
                // Re-inicializar listeners directos
                ResolucionsManagement.setupEditModal();
                ResolucionsManagement.setupDeleteModal();
                
                // Re-inicializar eventos en la nueva paginación
                bindPaginationEvents();
                
                // Actualizar URL
                window.history.replaceState(null, '', url);
            })
            .catch(error => {
                console.error('[Resolucions AJAX] Error:', error);
                if (tableSection) tableSection.style.opacity = '1';
                if (mobileSection) mobileSection.style.opacity = '1';
            });
        };

        const bindPaginationEvents = () => {
            const paginationContainer = document.getElementById('pagination-container');
            if (!paginationContainer) return;
            
            paginationContainer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    performAjaxSearch(this.href);
                });
            });
        };

        // Escuchar cambios de escritura en el subfiltro (con debounce)
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const params = getFilterParams();
                const url = window.location.pathname + '?' + params.toString();
                performAjaxSearch(url);
            }, 300);
        });

        // Interceptar submit del formulario del subfiltro
        const subfilterForm = document.getElementById('subfilterForm');
        if (subfilterForm) {
            subfilterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                const params = getFilterParams();
                const url = window.location.pathname + '?' + params.toString();
                performAjaxSearch(url);
            });
        }

        // Interceptar submit del formulario de la cabecera (RD y Asunto)
        const headerForm = document.getElementById('resolutionSearchForm');
        if (headerForm) {
            headerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                const params = getFilterParams();
                const url = window.location.pathname + '?' + params.toString();
                performAjaxSearch(url);
            });
        }

        // Interceptar submit del formulario del modal de filtros avanzados
        const modalFilterForm = document.getElementById('modalFilterForm');
        if (modalFilterForm) {
            modalFilterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                const params = getFilterParams();
                const url = window.location.pathname + '?' + params.toString();
                
                // Ocultar modal usando Bootstrap API
                const modalEl = document.getElementById('filterResolutionModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }

                performAjaxSearch(url);
            });
        }

        // Interceptar clics en el botón de limpiar filtros (para que sea AJAX y limpie inputs)
        document.addEventListener('click', (e) => {
            const cleanLink = e.target.closest('a[title="Limpiar todos los filtros"]');
            if (cleanLink) {
                e.preventDefault();
                // Limpiar inputs del DOM
                input.value = '';
                const searchRd = document.querySelector('input[name="search_rd"]');
                if (searchRd) searchRd.value = '';
                const searchAsunto = document.querySelector('input[name="search_asunto"]');
                if (searchAsunto) searchAsunto.value = '';
                
                // Resetear campos del modal de filtros avanzados
                const filterDesde = document.getElementById('filter_desde');
                if (filterDesde) filterDesde.value = '';
                const filterHasta = document.getElementById('filter_hasta');
                if (filterHasta) filterHasta.value = '';
                const filterPeriodo = document.getElementById('filter_periodo');
                if (filterPeriodo) filterPeriodo.value = '';
                const filterResType = document.getElementById('filter_resolution_type');
                if (filterResType) filterResType.value = '';
                const filterAsuntoType = document.getElementById('filter_asunto_type');
                if (filterAsuntoType) {
                    filterAsuntoType.value = '';
                    filterAsuntoType.dataset.selected = '';
                    filterAsuntoType.innerHTML = '<option value="">Seleccione tipo de resolución primero...</option>';
                    filterAsuntoType.disabled = true;
                }
                const filterLevel = document.getElementById('filter_level_modality');
                if (filterLevel) filterLevel.value = '';

                performAjaxSearch(cleanLink.href);
            }
        });

        // Inicializar eventos de la paginación al cargar la página por primera vez
        bindPaginationEvents();
    },

    setupSelectInteresadoModal: function() {
        const modalEl = document.getElementById('selectInteresadoModal');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        const form = document.getElementById('selectInteresadoForm');
        const listContainer = document.getElementById('modal_select_interesados_list');
        const rdEl = document.getElementById('modal_select_interesado_rd');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-trigger-select-interesado');
            if (!btn) return;

            e.preventDefault();
            
            const action = btn.dataset.action;
            const rd = btn.dataset.rd;
            let interesados = [];
            try {
                interesados = JSON.parse(btn.dataset.interesados);
            } catch (err) {
                console.error('Error parseando interesados para cargo:', err);
                return;
            }

            if (rdEl) rdEl.textContent = rd;
            if (form) form.action = action;
            if (listContainer) {
                listContainer.innerHTML = '';
                interesados.forEach(i => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 border-0 border-bottom';
                    item.innerHTML = `
                        <div class="d-flex flex-column text-start">
                            <span class="fw-bold text-dark small">${i.name}</span>
                            <span class="text-muted small" style="font-size: 0.75rem;">${i.type}</span>
                        </div>
                        <span class="material-symbols-outlined text-primary fs-5">chevron_right</span>
                    `;
                    item.onclick = () => {
                        document.getElementById('modal_select_interesado_id').value = i.id;
                        document.getElementById('modal_select_interesado_type').value = i.type;
                        
                        // Agregar feedback de carga al botón presionado
                        item.disabled = true;
                        item.innerHTML = `
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark small">${i.name}</span>
                                <span class="text-muted small" style="font-size: 0.75rem;">${i.type}</span>
                            </div>
                            <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                        `;
                        
                        form.submit();
                    };
                    listContainer.appendChild(item);
                });
            }

            modal.show();
        });
    },

    setupWorkConfirmation: function() {
        document.addEventListener('submit', function(e) {
            const form = e.target.closest('.form-work-resolution');
            if (form) {
                const confirmed = confirm('¿Está seguro de marcar esta resolución como trabajada? Esta acción no se puede deshacer.');
                if (!confirmed) {
                    e.preventDefault();
                }
            }
        });
    },

    setupModalAccessibility: function() {
        document.querySelectorAll('.modal').forEach(modalEl => {
            modalEl.addEventListener('hidden.bs.modal', () => {
                if (modalEl.contains(document.activeElement) || document.activeElement === modalEl) {
                    console.log(`[Accessibility] Foco liberado del modal oculto (${modalEl.id}) hacia document.body.`);
                    document.body.focus();
                }
            });
        });
    }
};
