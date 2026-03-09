document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('roles-page');
    if (!root) return;

    let permissionLabels = {};
    try {
        permissionLabels = JSON.parse(root.dataset.permissionLabels || '{}');
    } catch (e) {
        permissionLabels = {};
    }

    const hasSelect2 = window.jQuery && window.jQuery.fn && window.jQuery.fn.select2;

    const initSelect2 = (modalEl) => {
        if (!hasSelect2 || !modalEl) return;
        const $modal = window.jQuery(modalEl);
        $modal.find('.select2-permissions').each(function () {
            const $select = window.jQuery(this);
            if ($select.data('select2')) {
                $select.select2('destroy');
            }
            $select.select2({
                width: '100%',
                dropdownParent: $modal,
                placeholder: $select.data('placeholder') || 'Seleccionar',
                closeOnSelect: false,
            });
        });
    };

    const syncModuleFromSelect = (selectEl) => {
        const group = selectEl.closest('.permission-group');
        if (!group) return;
        const moduleCheckbox = group.querySelector('.permission-module, .edit-perm-module');
        if (!moduleCheckbox) return;
        moduleCheckbox.checked = selectEl.selectedOptions.length > 0;
    };

    const bindSelectSync = (rootEl) => {
        if (!rootEl) return;
        rootEl.querySelectorAll('.select2-permissions').forEach((selectEl) => {
            const handler = () => syncModuleFromSelect(selectEl);
            selectEl.addEventListener('change', handler);
            if (hasSelect2) {
                window
                    .jQuery(selectEl)
                    .on('select2:select', handler)
                    .on('select2:unselect', handler);
            }
            syncModuleFromSelect(selectEl);
        });
    };

    const setSelectValues = (selectEl, values) => {
        const normalized = values || [];
        Array.from(selectEl.options).forEach((option) => {
            option.selected = normalized.includes(option.value);
        });
        if (hasSelect2) {
            window.jQuery(selectEl).trigger('change.select2');
        }
        syncModuleFromSelect(selectEl);
    };

    const infoModalEl = document.getElementById('infoRoleModal');
    const infoModal = infoModalEl ? new bootstrap.Modal(infoModalEl) : null;
    const infoName = document.getElementById('info_role_name');
    const infoPerms = document.getElementById('info_role_permissions');

    document.querySelectorAll('.btn-role-info').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!infoModal || !infoName || !infoPerms) return;
            infoName.textContent = btn.dataset.name || '-';
            const perms = JSON.parse(btn.dataset.permissions || '[]');
            infoPerms.innerHTML = '';
            if (perms.length === 0) {
                infoPerms.innerHTML = '<span class="text-muted">Sin permisos</span>';
            } else {
                perms.forEach((p) => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-secondary';
                    badge.textContent = permissionLabels[p] || p;
                    infoPerms.appendChild(badge);
                });
            }
            infoModal.show();
        });
    });

    const editModalEl = document.getElementById('editRoleModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editForm = document.getElementById('editRoleForm');
    const editName = document.getElementById('edit_role_name');
    const editPermModules = document.querySelectorAll('.edit-perm-module');
    const editPermSelects = editModalEl ? editModalEl.querySelectorAll('.edit-perm-select') : [];
    const adminNotice = document.getElementById('edit_role_admin_notice');

    const createModalEl = document.getElementById('createRoleModal');
    createModalEl?.addEventListener('shown.bs.modal', () => {
        initSelect2(createModalEl);
        bindSelectSync(createModalEl);
    });
    editModalEl?.addEventListener('shown.bs.modal', () => {
        initSelect2(editModalEl);
        bindSelectSync(editModalEl);
    });

    document.querySelectorAll('.btn-edit-role').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!editModal || !editForm || !editName) return;
            const perms = JSON.parse(btn.dataset.permissions || '[]');
            const isAdmin = (btn.dataset.name || '').toUpperCase() === 'ADMINISTRADOR';
            editForm.action = btn.dataset.action || '';
            editName.value = btn.dataset.name || '';
            editPermModules.forEach((cb) => {
                cb.checked = perms.includes(cb.value);
                cb.disabled = isAdmin;
            });
            editPermSelects.forEach((selectEl) => {
                setSelectValues(selectEl, perms);
                selectEl.disabled = isAdmin;
            });
            if (adminNotice) {
                adminNotice.classList.toggle('d-none', !isAdmin);
            }
            editName.readOnly = isAdmin;
            editModal.show();
        });
    });
});
