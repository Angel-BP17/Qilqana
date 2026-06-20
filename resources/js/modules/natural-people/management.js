import { ApiLookup } from '../common/api-lookup';
import { ImportHelper } from '../common/import-helper';

export const NaturalPeopleManagement = {
    init: function() {
        this.setupEditModal();
        this.setupDeleteModal();
        this.setupLookup();
        this.setupImport();
        this.setupDocumentTypeToggle();
    },

    setupDocumentTypeToggle: function() {
        const setup = (prefix, selectId, dniContainerId, cedulaContainerId) => {
            const select = document.getElementById(selectId);
            const dniContainer = document.getElementById(dniContainerId);
            const cedulaContainer = document.getElementById(cedulaContainerId);

            if (!select) return;

            const toggle = () => {
                const isDni = select.value === 'DNI';
                if (dniContainer) dniContainer.classList.toggle('d-none', !isDni);
                if (cedulaContainer) cedulaContainer.classList.toggle('d-none', isDni);
            };

            select.addEventListener('change', toggle);
            toggle(); // Init
        };

        setup('create', 'create_doc_type', 'container_create_dni', 'container_create_cedula');
        setup('edit', 'edit_doc_type', 'container_edit_dni_local', 'container_edit_cedula_local');
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editNaturalPersonModal');
        if (!modalEl) return;
        const form = document.getElementById('editNaturalPersonForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-edit-natural-person');
            if (!btn) return;

            form.action = btn.dataset.action;
            
            const dni = btn.dataset.dni || '';
            const cedula = btn.dataset.cedula || '';
            
            document.getElementById('edit_dni').value = dni;
            document.getElementById('edit_cedula').value = cedula;
            document.getElementById('edit_nombres').value = btn.dataset.nombres || '';
            document.getElementById('edit_apellido_paterno').value = btn.dataset.apellidoPaterno || '';
            document.getElementById('edit_apellido_materno').value = btn.dataset.apellidoMaterno || '';
            
            // Ajustar el tipo de documento en el select
            const docTypeSelect = document.getElementById('edit_doc_type');
            if (docTypeSelect) {
                docTypeSelect.value = cedula ? 'CEDULA' : 'DNI';
                docTypeSelect.dispatchEvent(new Event('change'));
            }

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupLookup: function() {
        const bindLookup = (btnId, inputId, targets) => {
            const btn = document.getElementById(btnId);
            btn?.addEventListener('click', async () => {
                const val = document.getElementById(inputId)?.value;
                if (!val) return;
                
                this.setLoading(btn, true);
                const data = await ApiLookup.dni(val);
                this.setLoading(btn, false);

                if (data) {
                    alert(`Atención: La persona con DNI ${val} ya se encuentra registrada como ${data.nombres} ${data.apellido_paterno}.`);
                    
                    Object.keys(targets).forEach(key => {
                        const el = document.getElementById(targets[key]);
                        if (el) el.value = data[key] || '';
                    });
                }
            });
        };

        bindLookup('lookup_dni_btn', 'dni', { nombres: 'nombres', apellido_paterno: 'apellido_paterno', apellido_materno: 'apellido_materno' });
        bindLookup('lookup_dni_btn_edit', 'edit_dni', { nombres: 'edit_nombres', apellido_paterno: 'edit_apellido_paterno', apellido_materno: 'edit_apellido_materno' });
    },

    setLoading: function(btn, loading) {
        if (!btn) return;
        if (loading) {
            btn.dataset.originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined fa-spin">progress_activity</span>';
            btn.disabled = true;
        } else {
            btn.innerHTML = btn.dataset.originalHtml || 'Buscar';
            btn.disabled = false;
        }
    },

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteNaturalPersonModal');
        if (!modalEl) return;
        const form = document.getElementById('deleteNaturalPersonForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-delete-natural-person');
            if (!btn) return;

            form.action = btn.dataset.action;
            document.getElementById('delete_natural_person_reason').value = '';

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupImport: function() {
        ImportHelper.setup('natural-people', 'importNaturalPeopleButton');
        
        const confirmBtn = document.getElementById('confirmNaturalPeopleImport');
        const importForm = document.getElementById('naturalPeopleImportForm');
        if (confirmBtn && importForm) {
            confirmBtn.onclick = () => {
                const selected = document.querySelector('input[name="update_existing_choice"]:checked');
                document.getElementById('natural_people_update_existing').value = selected?.value ?? '1';
                importForm.submit();
            };
        }
    }
};
