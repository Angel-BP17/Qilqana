export async function loadSignatureData(btn, elements) {
    const { contentEl, signerEl, extraEl } = elements;
    const signatureUrl = btn.dataset.url || '';
    const signerName = btn.dataset.signer || '';
    const isTitular = btn.dataset.titularidad === '1';
    const titularName = btn.dataset.titularName || '';
    const titularDni = btn.dataset.titularDni || '';
    const parentesco = btn.dataset.parentesco || '';
    const evidenceUrl = btn.dataset.evidence || '';

    contentEl.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando firma...</p></div>';
    extraEl.style.display = 'none';
    extraEl.innerHTML = '';

    if (isTitular && (titularName || titularDni)) {
        const titularLabel = [titularName, titularDni].filter(Boolean).join(' - ');
        signerEl.style.display = 'block';
        signerEl.textContent = `Firmado por el titular: ${titularLabel}`;
    } else if (!isTitular && parentesco) {
        signerEl.style.display = 'block';
        signerEl.textContent = `Firmado por: ${parentesco}`;
    } else if (signerName) {
        signerEl.style.display = 'block';
        signerEl.textContent = `Firmado por: ${signerName}`;
    } else {
        signerEl.style.display = 'none';
    }

    if (evidenceUrl) {
        extraEl.style.display = 'block';
        extraEl.innerHTML = `<div class="text-muted small mb-2">Evidencia</div><img src="${evidenceUrl}" alt="Evidencia" class="img-fluid rounded border shadow-sm">`;
    }

    if (signatureUrl) {
        try {
            const response = await fetch(signatureUrl);
            if (response.ok) {
                contentEl.innerHTML = await response.text();
                return true;
            } else {
                throw new Error();
            }
        } catch (e) {
            contentEl.innerHTML = '<p class="text-danger mb-0">Error al cargar la firma.</p>';
            return false;
        }
    } else {
        contentEl.innerHTML = '<p class="text-muted mb-0">No hay firma disponible.</p>';
        return false;
    }
}
