import { ImportHelper } from '../common/import-helper';

export const ResolucionsManagement = {
    init: function() {
        this.setupDetailsModal();
        this.setupEditModal();
        this.setupDeleteModal();
        this.setupImport();
        this.setupFileLabel();
        this.setupSubfilter();
    },

    setupSubfilter: function() {
        const input = document.getElementById('subfilter-input');
        if (!input) return;

        input.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            // Filtrar Filas de Tabla (Escritorio)
            document.querySelectorAll('.table tbody tr').forEach(row => {
                if (row.cells.length <= 1) return; // Ignorar filas de "sin resultados"
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });

            // Filtrar Tarjetas (Móvil)
            document.querySelectorAll('.d-md-none .card.border-0.shadow-sm').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(query) ? '' : 'none';
            });
        });
    },

    setupDetailsModal: function() {
        const modalEl = document.getElementById('detailModal');
        if (!modalEl) return;

        $('#detailModal').on('show.bs.modal', function(event) {
            const btn = $(event.relatedTarget);
            const fields = ['rd', 'fecha', 'dni', 'apellidos', 'nombres', 'asunto', 'proyecto', 'expediente', 'fecha2', 'folios'];
            fields.forEach(f => $(`#modal-${f}`).text(btn.data(f) || '-'));
        });
    },

    setupEditModal: function() {
        const modalEl = document.getElementById('editResolutionModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('editResolutionForm');

        document.querySelectorAll('.btn-edit-resolution').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                const fields = ['rd', 'fecha', 'dni', 'nombres', 'procedencia', 'asunto'];
                fields.forEach(f => {
                    const el = document.getElementById(`edit_resolution_${f}`);
                    if (el) el.value = btn.dataset[f] || '';
                });
                modal.show();
            };
        });
    },

    setupDeleteModal: function() {
        const modalEl = document.getElementById('deleteResolutionModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('deleteResolutionForm');

        document.querySelectorAll('.btn-delete-resolution').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                document.getElementById('delete_resolution_reason').value = '';
                modal.show();
            };
        });
    },

    setupImport: function() {
        ImportHelper.setup('1', 'importExcelButton');
    },

    setupFileLabel: function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    }
};
