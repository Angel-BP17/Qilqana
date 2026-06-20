import { ApiLookup } from '../common/api-lookup';

export const UserManagement = {
    init: function() {
        this.setupInfoModal();
        this.setupEditModal();
        this.setupDeleteModal();
        this.setupLookup();
    },

    setupLookup: function() {
        const handleLookup = async (btnId, inputId, nameId, paternoId, maternoId, errorId, detailsClass) => {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const nameInput = document.getElementById(nameId);
            const paternoInput = document.getElementById(paternoId);
            const maternoInput = document.getElementById(maternoId);
            const errorEl = document.getElementById(errorId);
            const detailsContainers = document.querySelectorAll(detailsClass);

            if (!btn || !input) return;

            btn.addEventListener('click', async () => {
                const dni = input.value.trim();
                if (!dni) return;

                if (errorEl) errorEl.classList.add('d-none');
                this.setLoading(btn, true);

                try {
                    const data = await ApiLookup.dni(dni);
                    
                    // Siempre mostrar los campos después de buscar, independientemente del resultado
                    detailsContainers.forEach(el => el.classList.remove('d-none'));

                    if (data) {
                        if (nameInput) nameInput.value = data.nombres || '';
                        if (paternoInput) paternoInput.value = data.apellido_paterno || '';
                        if (maternoInput) maternoInput.value = data.apellido_materno || '';
                    } else {
                        // Limpiar campos si no se encuentra
                        if (nameInput) nameInput.value = '';
                        if (paternoInput) paternoInput.value = '';
                        if (maternoInput) maternoInput.value = '';
                        
                        if (errorEl) {
                            errorEl.textContent = 'No se encontró información o la API falló. Ingrese los datos manualmente.';
                            errorEl.classList.remove('d-none');
                        }
                    }
                } catch (error) {
                    console.error('[UserManagement] Error:', error);
                    detailsContainers.forEach(el => el.classList.remove('d-none'));
                    if (errorEl) {
                        errorEl.textContent = 'Error al consultar. Ingrese los datos manualmente.';
                        errorEl.classList.remove('d-none');
                    }
                } finally {
                    this.setLoading(btn, false);
                }
            });
        };

        // Para Crear
        handleLookup('lookup_user_dni_btn', 'create_user_dni', 'create_user_name', 'create_user_apellido_paterno', 'create_user_apellido_materno', 'create_user_dni_error', '.user-details-fields');

        // Para Editar
        handleLookup('lookup_user_dni_btn_edit', 'edit_dni', 'edit_name', 'edit_apellido_paterno', 'edit_apellido_materno', 'edit_user_dni_error', '.edit-user-details-fields');
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
            document.getElementById('edit_apellido_paterno').value = btn.dataset.apellido_paterno || '';
            document.getElementById('edit_apellido_materno').value = btn.dataset.apellido_materno || '';
            document.getElementById('edit_dni').value = btn.dataset.dni || '';

            // Mostrar campos si tienen datos
            document.querySelectorAll('.edit-user-details-fields').forEach(el => el.classList.remove('d-none'));
            
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
