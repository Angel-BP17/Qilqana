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

            const bootstrapInstance = window.bootstrap || bootstrap;
            let modal = bootstrapInstance.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrapInstance.Modal(modalEl);
            }
            modal.show();
        });
    }
};
