export const ViewerModule = {
    modals: {},
    init: function() {
        const signEl = document.getElementById('viewSignatureModal');
        const cartaEl = document.getElementById('viewCartaPoderModal');
        if (signEl) this.modals.viewSign = new bootstrap.Modal(signEl);
        if (cartaEl) this.modals.viewCarta = new bootstrap.Modal(cartaEl);
    },
    load: async function(url, container, isSvg = true) {
        container.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';
        try {
            const resp = await fetch(url);
            if (!resp.ok) throw new Error();
            if (isSvg) {
                container.innerHTML = await resp.text();
            } else {
                const blob = await resp.blob();
                const objUrl = URL.createObjectURL(blob);
                if (resp.headers.get('content-type')?.includes('pdf')) {
                    container.innerHTML = `<iframe src="${objUrl}" style="width:100%; height:70vh;"></iframe>`;
                } else {
                    container.innerHTML = `<img src="${objUrl}" class="img-fluid rounded shadow-sm">`;
                }
            }
        } catch { container.innerHTML = '<p class="text-danger p-3">Error al cargar archivo</p>'; }
    }
};
