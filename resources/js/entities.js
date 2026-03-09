document.addEventListener('DOMContentLoaded', () => {
    const editModalElement = document.getElementById('editEntityModal');
    const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
    const editForm = document.getElementById('editEntityForm');
    const editName = document.getElementById('edit_entity_name');
    const editCode = document.getElementById('edit_entity_code');
    const editDistrict = document.getElementById('edit_entity_district');
    const editContact = document.getElementById('edit_entity_contact');
    const editType = document.getElementById('edit_entity_type');

    document.querySelectorAll('.btn-edit-entity').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            editForm.action = btn.dataset.action || '';
            if (editName) editName.value = btn.dataset.name || '';
            if (editCode) editCode.value = btn.dataset.code || '';
            if (editDistrict) editDistrict.value = btn.dataset.district || '';
            if (editContact) editContact.value = btn.dataset.contact || '';
            if (editType) editType.value = btn.dataset.type || '';
            editModal?.show();
        });
    });

    const deleteModalElement = document.getElementById('deleteEntityModal');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteForm = document.getElementById('deleteEntityForm');
    const deleteReason = document.getElementById('delete_entity_reason');

    document.querySelectorAll('.btn-delete-entity').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!deleteModal || !deleteForm) return;
            deleteForm.action = btn.dataset.action || '';
            if (deleteReason) deleteReason.value = '';
            deleteModal.show();
        });
    });
});
