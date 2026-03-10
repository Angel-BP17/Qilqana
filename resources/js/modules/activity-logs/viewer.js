export const ActivityLogViewer = {
    init: function() {
        const modalEl = document.getElementById('activityChangesModal');
        if (!modalEl) return;

        const modal = new bootstrap.Modal(modalEl);
        const titleEl = document.getElementById('activityChangesModalLabel');
        const contentEl = document.getElementById('activityChangesContent');

        document.querySelectorAll('.btn-view-changes').forEach(btn => {
            btn.onclick = () => {
                const title = btn.dataset.title || 'Cambios';
                const changes = JSON.parse(btn.dataset.changes || '{}');
                
                titleEl.textContent = `Datos ${title}`;
                contentEl.innerHTML = '';

                Object.entries(changes).forEach(([key, value]) => {
                    const row = document.createElement('tr');
                    const valStr = (value === null || value === undefined) ? '' 
                                 : (typeof value === 'object' ? JSON.stringify(value) : String(value));
                    
                    row.innerHTML = `<th scope="row">${key}</th><td>${valStr}</td>`;
                    contentEl.appendChild(row);
                });
                modal.show();
            };
        });
    }
};
