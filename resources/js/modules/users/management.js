export const UserManagement = {
    init: function() {
        this.setupInfoModal();
        this.setupEditModal();
        this.setupDeleteModal();
    },

    setupInfoModal: function() {
        const modalEl = document.getElementById('userInfoModal');
        if (!modalEl) return;
        const fields = ['name', 'last_name', 'dni', 'user_type', 'created_at', 'updated_at'];

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-user-info');
            if (!btn) return;

            fields.forEach(f => {
                const el = document.getElementById(`info_${f}`);
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
        const modalEl = document.getElementById('editUserModal');
        if (!modalEl) return;
        const form = document.getElementById('editUserForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-edit-user');
            if (!btn) return;

            form.action = btn.dataset.action;
            document.getElementById('edit_name').value = btn.dataset.name || '';
            document.getElementById('edit_last_name').value = btn.dataset.last_name || '';
            document.getElementById('edit_dni').value = btn.dataset.dni || '';
            
            const roles = JSON.parse(btn.dataset.roles || '[]');
            document.querySelectorAll('.edit-role-checkbox').forEach(cb => {
                cb.checked = roles.includes(cb.value);
            });

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteUserModal');
        if (!modalEl) return;
        const form = document.getElementById('deleteUserForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-delete-user');
            if (!btn) return;

            form.action = btn.dataset.action;
            document.getElementById('delete_user_reason').value = '';

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    }
};
