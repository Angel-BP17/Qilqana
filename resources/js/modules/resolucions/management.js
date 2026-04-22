import { ImportHelper } from '../common/import-helper';
import { ApiLookup } from '../common/api-lookup';

export const ResolucionsManagement = {
    init: function() {
        this.setupDetailsModal();
        this.setupEditModal();
        this.setupDeleteModal();
        this.setupImport();
        this.setupFileLabel();
        this.setupSubfilter();
        this.setupFormSubmissionFeedback();
        this.setupLookup();
    },

    setupLookup: function() {
        const handleDniLookup = async (btnId, inputId, targetIds) => {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);

            if (!btn || !input) return;

            btn.addEventListener('click', async () => {
                const dni = input.value.trim();
                if (!dni) return;

                this.setLoading(btn, true);
                try {
                    const data = await ApiLookup.dni(dni);
                    if (data) {
                        // Autocompletar los 3 campos separados usando las propiedades del response
                        if (targetIds.nombres) {
                            const el = document.getElementById(targetIds.nombres);
                            if (el) el.value = data.nombres || '';
                        }
                        if (targetIds.paterno) {
                            const el = document.getElementById(targetIds.paterno);
                            if (el) el.value = data.apellido_paterno || '';
                        }
                        if (targetIds.materno) {
                            const el = document.getElementById(targetIds.materno);
                            if (el) el.value = data.apellido_materno || '';
                        }
                    }
                } catch (error) {
                    console.error('[Resolucions] Error en búsqueda de DNI:', error);
                } finally {
                    this.setLoading(btn, false);
                }
            });
        };

        handleDniLookup('lookup_dni_btn_resolutions', 'create_resolution_dni', {
            nombres: 'create_resolution_nombres',
            paterno: 'create_resolution_apellido_paterno',
            materno: 'create_resolution_apellido_materno'
        });
        
        handleDniLookup('lookup_dni_btn_resolutions_edit', 'edit_resolution_dni', {
            nombres: 'edit_resolution_nombres',
            paterno: 'edit_resolution_apellido_paterno',
            materno: 'edit_resolution_apellido_materno'
        });
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

    setupFormSubmissionFeedback: function() {
        const forms = [
            'resolutionFilterForm',
            'createResolutionForm',
            'editResolutionForm',
            'deleteResolutionForm'
        ];

        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.classList.add('btn-loading');
                    // Prevenir doble clic
                    btn.disabled = true;
                }
            });
        });

        // Caso especial para importación (Maatwebsite/Excel)
        const importForm = document.querySelector('form[action$="/import"]');
        if (importForm) {
            importForm.addEventListener('submit', function() {
                const btn = document.getElementById('importExcelButton');
                if (btn) {
                    btn.classList.add('btn-loading');
                    btn.disabled = true;
                }
            });
        }
    },

    setupSubfilter: function() {
        const input = document.getElementById('subfilter-input');
        const pagination = document.getElementById('pagination-container');
        const counterContainer = document.getElementById('subfilter-results-info');
        const counterBadge = document.getElementById('subfilter-counter');
        const noResultsMsg = document.getElementById('local-no-results');
        const globalSearchBtn = document.getElementById('btn-search-global');
        const filterInfo = document.getElementById('subfilter-info');

        if (!input) return;

        input.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const isFiltering = query.length > 0;
            let visibleCount = 0;
            
            // Ocultar paginación si se está filtrando localmente
            if (pagination) pagination.classList.toggle('d-none', isFiltering);
            if (filterInfo) filterInfo.classList.toggle('d-none', isFiltering);
            if (counterContainer) counterContainer.classList.toggle('d-none', !isFiltering);

            // Filtrar Filas de Tabla (Escritorio)
            const rows = document.querySelectorAll('.table tbody tr:not(.empty-row)');
            rows.forEach(row => {
                if (row.cells.length <= 1) return;
                const text = row.textContent.toLowerCase();
                const matches = text.includes(query);
                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });

            // Filtrar Tarjetas (Móvil)
            const cards = document.querySelectorAll('.d-md-none > .border.rounded-3.p-3.mb-3');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const matches = text.includes(query);
                card.style.display = matches ? '' : 'none';
                if (!rows.length && matches) visibleCount++; // Si no hay tabla, contar tarjetas
            });

            // Si hay tabla y tarjetas, priorizar el conteo de la tabla (evita duplicados)
            const finalCount = rows.length ? visibleCount : visibleCount;

            if (counterBadge) counterBadge.textContent = finalCount;
            if (noResultsMsg) noResultsMsg.classList.toggle('d-none', !isFiltering || finalCount > 0);
            
            // Ocultar las tablas/contenedores si no hay resultados para que el mensaje central se vea bien
            const tableContainer = document.querySelector('.table-responsive');
            const mobileContainer = document.querySelector('.d-md-none');
            if (tableContainer) tableContainer.classList.toggle('d-none', isFiltering && finalCount === 0);
            if (mobileContainer) mobileContainer.classList.toggle('d-none', isFiltering && finalCount === 0 && window.innerWidth < 768);
        });

        // Lógica del botón de búsqueda global
        if (globalSearchBtn) {
            globalSearchBtn.onclick = () => {
                const mainSearch = document.querySelector('input[name="search"]');
                const filterForm = document.getElementById('resolutionFilterForm');
                if (mainSearch && filterForm) {
                    mainSearch.value = input.value;
                    filterForm.submit();
                }
            };
        }
    },

    setupDetailsModal: function() {
        const modalEl = document.getElementById('detailModal');
        if (!modalEl) return;

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-view-resolution');
            if (!btn) return;

            const fields = ['rd', 'fecha', 'dni', 'nombres_apellidos', 'asunto', 'procedencia', 'periodo'];
            fields.forEach(f => {
                const el = document.getElementById(`modal-${f}`);
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
        const modalEl = document.getElementById('editResolutionModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('editResolutionForm');

        document.querySelectorAll('.btn-edit-resolution').forEach(btn => {
            btn.onclick = () => {
                form.action = btn.dataset.action;
                
                // Campos normales
                ['rd', 'fecha', 'dni', 'procedencia', 'asunto'].forEach(f => {
                    const el = document.getElementById(`edit_resolution_${f}`);
                    if (el) el.value = btn.dataset[f] || '';
                });

                // Para registros existentes, el nombre viene concatenado en un solo campo
                const nombresEl = document.getElementById('edit_resolution_nombres');
                if (nombresEl) nombresEl.value = btn.dataset.nombres || '';
                
                // Limpiar apellidos para obligar a nueva búsqueda o edición manual clara
                const paternoEl = document.getElementById('edit_resolution_apellido_paterno');
                const maternoEl = document.getElementById('edit_resolution_apellido_materno');
                if (paternoEl) paternoEl.value = '';
                if (maternoEl) maternoEl.value = '';

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
