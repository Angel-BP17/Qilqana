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
        const form = document.getElementById('editLegalEntityForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-edit-legal-entity');
            if (!btn) return;

            form.action = btn.dataset.action;
            document.getElementById('edit_ruc').value = btn.dataset.ruc || '';
            document.getElementById('edit_razon_social').value = btn.dataset.razon || '';
            document.getElementById('edit_district').value = btn.dataset.district || '';
            
            const p = 'edit_representative_';
            document.getElementById(p + 'dni').value = btn.dataset.representativeDni || '';
            document.getElementById(p + 'nombres').value = btn.dataset.representativeNombres || '';
            document.getElementById(p + 'apellido_paterno').value = btn.dataset.representativeApellidoPaterno || '';
            document.getElementById(p + 'apellido_materno').value = btn.dataset.representativeApellidoMaterno || '';
            document.getElementById(p + 'cargo').value = btn.dataset.representativeCargo || '';
            document.getElementById(p + 'since').value = btn.dataset.representativeSince || '';
            
            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupLookup: function() {
        const fillFields = (data, targets) => {
            Object.entries(targets).forEach(([key, id]) => {
                const el = document.getElementById(id);
                if (el) el.value = data[key] || '';
            });
        };

        // Búsqueda RUC (Crea)
        const rucBtn = document.getElementById('lookup_ruc_btn');
        rucBtn?.addEventListener('click', async () => {
            const val = document.getElementById('ruc')?.value;
            if (!val) return;
            this.setLoading(rucBtn, true);
            const data = await ApiLookup.ruc(val);
            this.setLoading(rucBtn, false);
            if (data) {
                if (data.razon_social) alert(`Atención: La entidad ${val} ya existe como ${data.razon_social}`);
                fillFields(data, { ruc: 'ruc', razon_social: 'razon_social', district: 'district' });
                
                if (data.representative) {
                    const rep = data.representative;
                    const p = 'representative_';
                    document.getElementById(p + 'dni').value = rep.dni || '';
                    document.getElementById(p + 'nombres').value = rep.nombres || '';
                    document.getElementById(p + 'apellido_paterno').value = rep.apellido_paterno || '';
                    document.getElementById(p + 'apellido_materno').value = rep.apellido_materno || '';
                    document.getElementById(p + 'cargo').value = rep.cargo || '';
                    document.getElementById(p + 'since').value = rep.fecha_desde || '';
                }
            }
        });

        // Búsqueda DNI Representante (Crea)
        const repBtn = document.getElementById('lookup_representative_dni_btn_entities');
        repBtn?.addEventListener('click', async () => {
            const val = document.getElementById('representative_dni')?.value;
            if (!val) return;
            this.setLoading(repBtn, true);
            const data = await ApiLookup.dni(val);
            this.setLoading(repBtn, false);
            if (data) {
                fillFields(data, {
                    nombres: 'representative_nombres',
                    apellido_paterno: 'representative_apellido_paterno',
                    apellido_materno: 'representative_apellido_materno'
                });
            }
        });

        // Búsqueda RUC (Edita)
        const rucBtnEdit = document.getElementById('lookup_ruc_btn_edit');
        rucBtnEdit?.addEventListener('click', async () => {
            const val = document.getElementById('edit_ruc')?.value;
            if (!val) return;
            this.setLoading(rucBtnEdit, true);
            const data = await ApiLookup.ruc(val);
            this.setLoading(rucBtnEdit, false);
            if (data) {
                fillFields(data, { ruc: 'edit_ruc', razon_social: 'edit_razon_social', district: 'edit_district' });
            }
        });

        // Búsqueda DNI Representante (Edita)
        const repBtnEdit = document.getElementById('lookup_representative_dni_btn_entities_edit');
        repBtnEdit?.addEventListener('click', async () => {
            const val = document.getElementById('edit_representative_dni')?.value;
            if (!val) return;
            this.setLoading(repBtnEdit, true);
            const data = await ApiLookup.dni(val);
            this.setLoading(repBtnEdit, false);
            if (data) {
                fillFields(data, {
                    nombres: 'edit_representative_nombres',
                    apellido_paterno: 'edit_representative_apellido_paterno',
                    apellido_materno: 'edit_representative_apellido_materno'
                });
            }
        });
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
        const modalEl = document.getElementById('deleteLegalEntityModal');
        if (!modalEl) return;
        const form = document.getElementById('deleteLegalEntityForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-delete-legal-entity');
            if (!btn) return;

            form.action = btn.dataset.action;
            const reasonEl = document.getElementById('delete_legal_entity_reason');
            if (reasonEl) reasonEl.value = '';
            
            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupImport: function() {
        ImportHelper.setup('legal-entities', 'importLegalEntitiesButton');
    }
};
