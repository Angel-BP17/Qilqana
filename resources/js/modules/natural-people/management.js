import { ApiLookup } from '../common/api-lookup';
import { ImportHelper } from '../common/import-helper';

export const NaturalPeopleManagement = {
    init: function() {
        this.setupEditModal();
        this.setupDeleteModal();
        this.setupLookup();
        this.setupImport();
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editNaturalPersonModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('editNaturalPersonForm');

        document.querySelectorAll('.btn-edit-natural-person').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('edit_dni').value = btn.dataset.dni || '';
                document.getElementById('edit_nombres').value = btn.dataset.nombres || '';
                document.getElementById('edit_apellido_paterno').value = btn.dataset.apellidoPaterno || '';
                document.getElementById('edit_apellido_materno').value = btn.dataset.apellidoMaterno || '';
                modal.show();
            };
        });
    },

    setupLookup: function() {
        const bindLookup = (btnId, inputId, targets) => {
            document.getElementById(btnId)?.addEventListener('click', async () => {
                const data = await ApiLookup.dni(document.getElementById(inputId)?.value);
                if (data) {
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

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteNaturalPersonModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('deleteNaturalPersonForm');

        document.querySelectorAll('.btn-delete-natural-person').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('delete_natural_person_reason').value = '';
                modal.show();
            };
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
