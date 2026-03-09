document.addEventListener('DOMContentLoaded', () => {
    const editModalElement = document.getElementById('editLegalEntityModal');
    const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
    const editForm = document.getElementById('editLegalEntityForm');
    const editRuc = document.getElementById('edit_ruc');
    const editRazon = document.getElementById('edit_razon_social');
    const editDistrict = document.getElementById('edit_district');
    const editRepresentative = document.getElementById('edit_representative_id');
    const editRepresentativeDni = document.getElementById('edit_representative_dni');
    const editRepresentativeName = document.getElementById('edit_representative_name');
    const editRepresentativeCargo = document.getElementById('edit_representative_cargo');
    const editRepresentativeSince = document.getElementById('edit_representative_since');

    document.querySelectorAll('.btn-edit-legal-entity').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            editForm.action = btn.dataset.action || '';
            if (editRuc) editRuc.value = btn.dataset.ruc || '';
            if (editRazon) editRazon.value = btn.dataset.razon || '';
            if (editDistrict) editDistrict.value = btn.dataset.district || '';
            if (editRepresentative) editRepresentative.value = btn.dataset.representative || '';
            if (editRepresentativeDni)
                editRepresentativeDni.value = btn.dataset.representativeDni || '';
            if (editRepresentativeName)
                editRepresentativeName.value = btn.dataset.representativeName || '';
            if (editRepresentativeCargo)
                editRepresentativeCargo.value = btn.dataset.representativeCargo || '';
            if (editRepresentativeSince)
                editRepresentativeSince.value = btn.dataset.representativeSince || '';

            editModal?.show();
        });
    });

    const deleteModalElement = document.getElementById('deleteLegalEntityModal');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteForm = document.getElementById('deleteLegalEntityForm');
    const deleteReason = document.getElementById('delete_legal_entity_reason');

    const lookupRuc = async (ruc, target) => {
        const clean = (ruc || '').trim();
        if (!clean) return;
        try {
            const res = await fetch(`/api/legal-entities/by-ruc/${encodeURIComponent(clean)}`);
            if (!res.ok) return;
            const payload = await res.json();
            const data = payload.data || {};
            if (target.ruc) target.ruc.value = data.ruc || '';
            if (target.razon) target.razon.value = data.razon_social || '';
            if (target.district) target.district.value = data.district || '';
            if (target.representative) target.representative.value = data.representative?.id || '';
            if (target.repDni) target.repDni.value = data.representative?.dni || '';
            if (target.repName) target.repName.value = data.representative?.nombre || '';
            if (target.repCargo) target.repCargo.value = data.representative?.cargo || '';
            if (target.repSince) target.repSince.value = data.representative?.fecha_desde || '';
        } catch (e) {
            console.error(e);
        }
    };

    const lookupBtn = document.getElementById('lookup_ruc_btn');
    if (lookupBtn) {
        lookupBtn.addEventListener('click', () => {
            lookupRuc(document.getElementById('ruc')?.value, {
                ruc: document.getElementById('ruc'),
                razon: document.getElementById('razon_social'),
                district: document.getElementById('district'),
                representative: document.getElementById('representative_id'),
                repDni: document.getElementById('representative_dni'),
                repName: document.getElementById('representative_name'),
                repCargo: document.getElementById('representative_cargo'),
                repSince: document.getElementById('representative_since'),
            });
        });
    }

    const lookupBtnEdit = document.getElementById('lookup_ruc_btn_edit');
    if (lookupBtnEdit) {
        lookupBtnEdit.addEventListener('click', () => {
            lookupRuc(document.getElementById('edit_ruc')?.value, {
                ruc: document.getElementById('edit_ruc'),
                razon: document.getElementById('edit_razon_social'),
                district: document.getElementById('edit_district'),
                representative: document.getElementById('edit_representative_id'),
                repDni: document.getElementById('edit_representative_dni'),
                repName: document.getElementById('edit_representative_name'),
                repCargo: document.getElementById('edit_representative_cargo'),
                repSince: document.getElementById('edit_representative_since'),
            });
        });
    }

    document.querySelectorAll('.btn-delete-legal-entity').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!deleteModal || !deleteForm) return;
            deleteForm.action = btn.dataset.action || '';
            if (deleteReason) deleteReason.value = '';
            deleteModal.show();
        });
    });

    const importInput = document.querySelector('[data-import-input="legal-entities"]');
    const importBtn = document.getElementById('importLegalEntitiesButton');
    if (importInput && importBtn) {
        const toggleImportButton = () => {
            importBtn.disabled = !importInput.files || importInput.files.length === 0;
        };
        toggleImportButton();
        importInput.addEventListener('change', toggleImportButton);
    }
});
