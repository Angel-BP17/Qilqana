document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('activityChangesModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
    const titleEl = document.getElementById('activityChangesModalLabel');
    const contentEl = document.getElementById('activityChangesContent');

    document.querySelectorAll('.btn-view-changes').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!modal || !titleEl || !contentEl) return;
            const title = btn.dataset.title || 'Cambios';
            const raw = btn.dataset.changes || '{}';
            let parsed = {};
            try {
                parsed = JSON.parse(raw);
            } catch (e) {
                parsed = raw;
            }
            titleEl.textContent = `Datos ${title}`;
            contentEl.innerHTML = '';
            if (parsed && typeof parsed === 'object') {
                Object.entries(parsed).forEach(([key, value]) => {
                    const row = document.createElement('tr');
                    const keyCell = document.createElement('th');
                    keyCell.scope = 'row';
                    keyCell.textContent = key;
                    const valueCell = document.createElement('td');
                    if (value === null || value === undefined || value === '') {
                        valueCell.textContent = '';
                    } else if (typeof value === 'object') {
                        valueCell.textContent = JSON.stringify(value);
                    } else {
                        valueCell.textContent = String(value);
                    }
                    row.appendChild(keyCell);
                    row.appendChild(valueCell);
                    contentEl.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                const keyCell = document.createElement('th');
                keyCell.scope = 'row';
                keyCell.textContent = 'Valor';
                const valueCell = document.createElement('td');
                valueCell.textContent = parsed ? String(parsed) : '';
                row.appendChild(keyCell);
                row.appendChild(valueCell);
                contentEl.appendChild(row);
            }
            modal.show();
        });
    });
});
