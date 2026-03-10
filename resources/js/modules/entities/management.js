export const EntitiesManagement = {
    init: function() {
        this.setupEditModal();
        this.setupDeleteModal();
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editEntityModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('editEntityForm');

        document.querySelectorAll('.btn-edit-entity').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                const fields = ['name', 'code', 'district', 'contact', 'type'];
                fields.forEach(f => {
                    const el = document.getElementById(`edit_entity_${f}`);
                    if (el) el.value = btn.dataset[f] || '';
                });
                modal.show();
            };
        });
    },

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteEntityModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('deleteEntityForm');

        document.querySelectorAll('.btn-delete-entity').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                const reasonEl = document.getElementById('delete_entity_reason');
                if (reasonEl) reasonEl.value = '';
                modal.show();
            };
        });
    }
};
