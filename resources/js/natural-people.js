document.addEventListener('DOMContentLoaded', () => {
    const editModalElement = document.getElementById('editNaturalPersonModal');
    const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
    const editForm = document.getElementById('editNaturalPersonForm');

    document.querySelectorAll('.btn-edit-natural-person').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            editForm.action = btn.dataset.action || '';
            const setValue = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = value || '';
            };
            setValue('edit_dni', btn.dataset.dni);
            setValue('edit_nombres', btn.dataset.nombres);
            setValue('edit_apellido_paterno', btn.dataset.apellidoPaterno);
            setValue('edit_apellido_materno', btn.dataset.apellidoMaterno);
            editModal?.show();
        });
    });

    const deleteModalElement = document.getElementById('deleteNaturalPersonModal');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteForm = document.getElementById('deleteNaturalPersonForm');
    const deleteReason = document.getElementById('delete_natural_person_reason');

    const lookupDni = async (dni, target) => {
        const clean = (dni || '').trim();
        if (!clean) return;
        try {
            const res = await fetch(`/api/natural-people/by-dni/${encodeURIComponent(clean)}`);
            if (!res.ok) return;
            const payload = await res.json();
            const data = payload.data || {};
            if (target.dni) target.dni.value = data.dni || '';
            if (target.nombres) target.nombres.value = data.nombres || '';
            if (target.apellidoPaterno) target.apellidoPaterno.value = data.apellido_paterno || '';
            if (target.apellidoMaterno) target.apellidoMaterno.value = data.apellido_materno || '';
        } catch (e) {
            console.error(e);
        }
    };

    const lookupBtn = document.getElementById('lookup_dni_btn');
    if (lookupBtn) {
        lookupBtn.addEventListener('click', () => {
            lookupDni(document.getElementById('dni')?.value, {
                dni: document.getElementById('dni'),
                nombres: document.getElementById('nombres'),
                apellidoPaterno: document.getElementById('apellido_paterno'),
                apellidoMaterno: document.getElementById('apellido_materno'),
            });
        });
    }

    const lookupBtnEdit = document.getElementById('lookup_dni_btn_edit');
    if (lookupBtnEdit) {
        lookupBtnEdit.addEventListener('click', () => {
            lookupDni(document.getElementById('edit_dni')?.value, {
                dni: document.getElementById('edit_dni'),
                nombres: document.getElementById('edit_nombres'),
                apellidoPaterno: document.getElementById('edit_apellido_paterno'),
                apellidoMaterno: document.getElementById('edit_apellido_materno'),
            });
        });
    }

    document.querySelectorAll('.btn-delete-natural-person').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!deleteModal || !deleteForm) return;
            deleteForm.action = btn.dataset.action || '';
            if (deleteReason) deleteReason.value = '';
            deleteModal.show();
        });
    });

    const importInput = document.querySelector('[data-import-input="natural-people"]');
    const importBtn = document.getElementById('importNaturalPeopleButton');
    const importForm = document.getElementById('naturalPeopleImportForm');
    const importConfirmBtn = document.getElementById('confirmNaturalPeopleImport');
    const updateExistingInput = document.getElementById('natural_people_update_existing');
    if (importInput && importBtn) {
        const toggleImportButton = () => {
            importBtn.disabled = !importInput.files || importInput.files.length === 0;
        };
        toggleImportButton();
        importInput.addEventListener('change', toggleImportButton);
    }

    if (importConfirmBtn && importForm && updateExistingInput) {
        importConfirmBtn.addEventListener('click', () => {
            const selected = document.querySelector('input[name="update_existing_choice"]:checked');
            updateExistingInput.value = selected?.value ?? '1';
            importForm.submit();
        });
    }
});
