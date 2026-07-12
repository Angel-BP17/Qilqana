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
        
        const nameEl = document.getElementById('info_role_name');
        const permsEl = document.getElementById('info_role_permissions');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-role-info');
            if (!btn) return;

            nameEl.textContent = btn.dataset.name || '-';
            const perms = JSON.parse(btn.dataset.permissions || '[]');
            permsEl.innerHTML = perms.length ? perms.map(p => `<span class="badge bg-secondary">${this.permissionLabels[p] || p}</span>`).join(' ') : '<span class="text-muted">Sin permisos</span>';
            
            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editRoleModal');
        if (!modalEl) return;
        
        const form = document.getElementById('editRoleForm');
        const nameInput = document.getElementById('edit_role_name');
        const adminNotice = document.getElementById('edit_role_admin_notice');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-edit-role');
            if (!btn) return;

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
                const selectedVals = perms.filter(val => Array.from(select.options).some(opt => opt.value === val));
                if (select.tomselect) {
                    select.tomselect.setValue(selectedVals);
                    if (isAdmin) {
                        select.tomselect.disable();
                    } else {
                        select.tomselect.enable();
                    }
                } else {
                    Array.from(select.options).forEach(opt => opt.selected = perms.includes(opt.value));
                    select.disabled = isAdmin;
                }
            });

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    },

    setupSelect2Sync: function() {
        const sync = (select) => {
            const group = select.closest('.permission-group');
            const checkbox = group?.querySelector('.permission-module, .edit-perm-module');
            if (checkbox) checkbox.checked = select.selectedOptions.length > 0;
        };

        const initSelect2 = (modal) => {
            if (!window.TomSelect) return;
            modal.querySelectorAll('.select2-permissions').forEach(select => {
                if (select.tomselect) {
                    sync(select);
                    return;
                }
                const ts = new TomSelect(select, {
                    plugins: ['remove_button'],
                    maxItems: null,
                    closeOnSelect: false,
                    placeholder: 'Seleccione permisos...'
                });
                ts.on('change', () => sync(select));
                sync(select);
            });
        };

        document.getElementById('createRoleModal')?.addEventListener('shown.bs.modal', function() { initSelect2(this); });
        document.getElementById('editRoleModal')?.addEventListener('shown.bs.modal', function() { initSelect2(this); });
    }
};
