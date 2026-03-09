document.addEventListener('DOMContentLoaded', () => {
    const infoModalElement = document.getElementById('userInfoModal');
    if (!infoModalElement) return;

    const infoModal = new bootstrap.Modal(infoModalElement);
    const infoFields = {
        name: document.getElementById('info_name'),
        last_name: document.getElementById('info_last_name'),
        dni: document.getElementById('info_dni'),
        user_type: document.getElementById('info_user_type'),
        created_at: document.getElementById('info_created_at'),
        updated_at: document.getElementById('info_updated_at'),
    };

    document.querySelectorAll('.btn-user-info').forEach((btn) => {
        btn.addEventListener('click', () => {
            infoFields.name.textContent = btn.dataset.name || '-';
            infoFields.last_name.textContent = btn.dataset.last_name || '-';
            infoFields.dni.textContent = btn.dataset.dni || '-';
            infoFields.user_type.textContent = btn.dataset.user_type || '-';
            infoFields.created_at.textContent = btn.dataset.created_at || '-';
            infoFields.updated_at.textContent = btn.dataset.updated_at || '-';
            infoModal.show();
        });
    });

    const editModalElement = document.getElementById('editUserModal');
    const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
    const editForm = document.getElementById('editUserForm');
    const editName = document.getElementById('edit_name');
    const editLastName = document.getElementById('edit_last_name');
    const editDni = document.getElementById('edit_dni');
    const editRoleCheckboxes = document.querySelectorAll('.edit-role-checkbox');

    document.querySelectorAll('.btn-edit-user').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            editForm.action = btn.dataset.action || '';
            if (editName) editName.value = btn.dataset.name || '';
            if (editLastName) editLastName.value = btn.dataset.last_name || '';
            if (editDni) editDni.value = btn.dataset.dni || '';
            const roles = JSON.parse(btn.dataset.roles || '[]');
            editRoleCheckboxes.forEach((cb) => {
                cb.checked = roles.includes(cb.value);
            });

            editModal?.show();
        });
    });

    const deleteModalElement = document.getElementById('deleteUserModal');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteForm = document.getElementById('deleteUserForm');
    const deleteReason = document.getElementById('delete_user_reason');

    document.querySelectorAll('.btn-delete-user').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!deleteModal || !deleteForm) return;
            deleteForm.action = btn.dataset.action || '';
            if (deleteReason) {
                deleteReason.value = '';
            }
            deleteModal.show();
        });
    });
});
