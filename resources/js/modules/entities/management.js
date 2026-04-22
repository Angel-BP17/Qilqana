export const EntitiesManagement = {
    init: function() {
        this.setupEditModal();
        this.setupDeleteModal();
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editEntityModal');
        if (!modalEl) return;
        const form = document.getElementById('editEntityForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-edit-entity');
            if (!btn) return;

            form.action = btn.dataset.action;
            const fields = ['name', 'code', 'district', 'contact', 'type'];
            fields.forEach(f => {
                const el = document.getElementById(`edit_entity_${f}`);
                if (el) el.value = btn.dataset[f] || '';
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
        const modalEl = document.getElementById('deleteEntityModal');
        if (!modalEl) return;
        const form = document.getElementById('deleteEntityForm');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-delete-entity');
            if (!btn) return;

            form.action = btn.dataset.action;
            const reasonEl = document.getElementById('delete_entity_reason');
            if (reasonEl) reasonEl.value = '';

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    }
};
