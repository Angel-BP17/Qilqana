export const ActivityLogViewer = {
    init: function() {
        const modalEl = document.getElementById('activityChangesModal');
        if (!modalEl) return;

        const titleEl = document.getElementById('activityChangesModalLabel');
        const contentEl = document.getElementById('activityChangesContent');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-view-changes');
            if (!btn) return;

            const title = btn.dataset.title || 'Cambios';
            let changes = {};

            try {
                const rawData = btn.dataset.changes;
                if (typeof rawData === 'string') {
                    changes = JSON.parse(rawData || '{}');
                } else if (typeof rawData === 'object' && rawData !== null) {
                    changes = rawData;
                }
            } catch (error) {
                console.error('[ActivityLog] Error parsing changes data:', error);
                changes = { error: 'No se pudieron cargar los detalles del cambio' };
            }
            
            titleEl.textContent = `Datos ${title}`;
            contentEl.innerHTML = '';

            if (changes === null || Object.keys(changes).length === 0) {
                contentEl.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Sin datos registrados</td></tr>';
            } else {
                Object.entries(changes).forEach(([key, value]) => {
                    const row = document.createElement('tr');
                    let valStr = '';

                    if (value === null || value === undefined) {
                        valStr = '<em class="text-muted">null</em>';
                    } else if (typeof value === 'object') {
                        valStr = `<pre class="mb-0 small bg-light p-2">${JSON.stringify(value, null, 2)}</pre>`;
                    } else {
                        valStr = String(value);
                    }
                    
                    row.innerHTML = `<th scope="row" class="bg-light" style="width: 30%">${key}</th><td>${valStr}</td>`;
                    contentEl.appendChild(row);
                });
            }

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    }
};
