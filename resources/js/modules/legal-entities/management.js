import { ApiLookup } from '../common/api-lookup';
import { ImportHelper } from '../common/import-helper';

export const LegalEntitiesManagement = {
    init: function() {
        this.setupEditModal();
        this.setupDeleteModal();
        this.setupLookup();
        this.setupImport();
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editLegalEntityModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('editLegalEntityForm');

        document.querySelectorAll('.btn-edit-legal-entity').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('edit_ruc').value = btn.dataset.ruc || '';
                document.getElementById('edit_razon_social').value = btn.dataset.razon || '';
                document.getElementById('edit_district').value = btn.dataset.district || '';
                document.getElementById('edit_representative_id').value = btn.dataset.representative || '';
                document.getElementById('edit_representative_dni').value = btn.dataset.representativeDni || '';
                document.getElementById('edit_representative_name').value = btn.dataset.representativeName || '';
                document.getElementById('edit_representative_cargo').value = btn.dataset.representativeCargo || '';
                document.getElementById('edit_representative_since').value = btn.dataset.representativeSince || '';
                modal.show();
            };
        });
    },

    setupLookup: function() {
        const bindLookup = (btnId, inputId, targets) => {
            document.getElementById(btnId)?.addEventListener('click', async () => {
                const data = await ApiLookup.ruc(document.getElementById(inputId)?.value);
                if (data) {
                    Object.keys(targets).forEach(key => {
                        const el = document.getElementById(targets[key]);
                        if (el) el.value = data[key] || (data.representative && data.representative[key]) || '';
                    });
                }
            });
        };

        const targets = {
            ruc: 'ruc', razon_social: 'razon_social', district: 'district',
            dni: 'representative_dni', nombre: 'representative_name', cargo: 'representative_cargo', fecha_desde: 'representative_since'
        };
        const editTargets = {
            ruc: 'edit_ruc', razon_social: 'edit_razon_social', district: 'edit_district',
            dni: 'edit_representative_dni', nombre: 'edit_representative_name', cargo: 'edit_representative_cargo', fecha_desde: 'edit_representative_since'
        };

        bindLookup('lookup_ruc_btn', 'ruc', targets);
        bindLookup('lookup_ruc_btn_edit', 'edit_ruc', editTargets);
    },

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteLegalEntityModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('deleteLegalEntityForm');

        document.querySelectorAll('.btn-delete-legal-entity').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('delete_legal_entity_reason').value = '';
                modal.show();
            };
        });
    },

    setupImport: function() {
        ImportHelper.setup('legal-entities', 'importLegalEntitiesButton');
    }
};
