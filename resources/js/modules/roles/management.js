export const RolesManagement = {
    init: function() {
        const root = document.getElementById('roles-page');
        if (!root) return;

        this.permissionLabels = JSON.parse(root.dataset.permissionLabels || '{}');
        this.setupInfoModal();
        this.setupEditModal();
        this.setupSelect2Sync();
    },

    setupInfoModal: function() {
        const modalEl = document.getElementById('infoRoleModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const nameEl = document.getElementById('info_role_name');
        const permsEl = document.getElementById('info_role_permissions');

        document.querySelectorAll('.btn-role-info').forEach(btn => {
            btn.onclick = () => {
                nameEl.textContent = btn.dataset.name || '-';
                const perms = JSON.parse(btn.dataset.permissions || '[]');
                permsEl.innerHTML = perms.length ? perms.map(p => `<span class="badge bg-secondary">${this.permissionLabels[p] || p}</span>`).join(' ') : '<span class="text-muted">Sin permisos</span>';
                modal.show();
            };
        });
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editRoleModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('editRoleForm');
        const nameInput = document.getElementById('edit_role_name');
        const adminNotice = document.getElementById('edit_role_admin_notice');

        document.querySelectorAll('.btn-edit-role').forEach(btn => {
            btn.onclick = () => {
                const isAdmin = (btn.dataset.name || '').toUpperCase() === 'ADMINISTRADOR';
                form.action = btn.dataset.action;
                nameInput.value = btn.dataset.name || '';
                nameInput.readOnly = isAdmin;
                if (adminNotice) adminNotice.classList.toggle('d-none', !isAdmin);

                const perms = JSON.parse(btn.dataset.permissions || '[]');
                document.querySelectorAll('.edit-perm-module').forEach(cb => {
                    cb.checked = perms.includes(cb.value);
                    cb.disabled = isAdmin;
                });
                document.querySelectorAll('.edit-perm-select').forEach(select => {
                    Array.from(select.options).forEach(opt => opt.selected = perms.includes(opt.value));
                    if (window.jQuery) window.jQuery(select).trigger('change.select2');
                    select.disabled = isAdmin;
                });
                modal.show();
            };
        });
    },

    setupSelect2Sync: function() {
        const sync = (select) => {
            const group = select.closest('.permission-group');
            const checkbox = group?.querySelector('.permission-module, .edit-perm-module');
            if (checkbox) checkbox.checked = select.selectedOptions.length > 0;
        };

        const initSelect2 = (modal) => {
            if (!window.jQuery?.fn?.select2) return;
            window.jQuery(modal).find('.select2-permissions').each(function() {
                const $s = window.jQuery(this);
                $s.select2({ width: '100%', dropdownParent: window.jQuery(modal), closeOnSelect: false });
                $s.on('change', () => sync(this));
                sync(this);
            });
        };

        document.getElementById('createRoleModal')?.addEventListener('shown.bs.modal', function() { initSelect2(this); });
        document.getElementById('editRoleModal')?.addEventListener('shown.bs.modal', function() { initSelect2(this); });
    }
};
