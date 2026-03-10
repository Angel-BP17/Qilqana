export const UserManagement = {
    init: function() {
        this.setupInfoModal();
        this.setupEditModal();
        this.setupDeleteModal();
    },

    setupInfoModal: function() {
        const modalEl = document.getElementById('userInfoModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const fields = ['name', 'last_name', 'dni', 'user_type', 'created_at', 'updated_at'];

        document.querySelectorAll('.btn-user-info').forEach(btn => {
            btn.onclick = () => {
                fields.forEach(f => {
                    const el = document.getElementById(`info_${f}`);
                    if (el) el.textContent = btn.dataset[f] || '-';
                });
                modal.show();
            };
        });
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editUserModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('editUserForm');

        document.querySelectorAll('.btn-edit-user').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('edit_name').value = btn.dataset.name || '';
                document.getElementById('edit_last_name').value = btn.dataset.last_name || '';
                document.getElementById('edit_dni').value = btn.dataset.dni || '';
                
                const roles = JSON.parse(btn.dataset.roles || '[]');
                document.querySelectorAll('.edit-role-checkbox').forEach(cb => {
                    cb.checked = roles.includes(cb.value);
                });
                modal.show();
            };
        });
    },

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteUserModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('deleteUserForm');

        document.querySelectorAll('.btn-delete-user').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('delete_user_reason').value = '';
                modal.show();
            };
        });
    }
};
