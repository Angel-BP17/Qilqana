export const ViewerModule = {
    modals: {},
    init: function() {
        const detailsEl = document.getElementById('viewChargeDetailsModal');
        if (detailsEl) {
            this.modals.viewDetails = new bootstrap.Modal(detailsEl);
        }
    },

    showDetails: function(d) {
        // Población de datos básicos
        document.getElementById('view_charge_number').textContent = d.nCharge;
        document.getElementById('view_charge_interesado').textContent = d.interesado;
        document.getElementById('view_charge_tipo').textContent = d.tipo;
        document.getElementById('view_charge_asunto').textContent = d.asunto;
        document.getElementById('view_charge_fecha').textContent = d.fecha;
        document.getElementById('view_charge_user').textContent = d.user;
        document.getElementById('view_charge_status').textContent = d.status;

        // Gestión de Secciones (Firma, Evidencia, Carta Poder)
        this.handleSignatureSection(d);
        this.handleEvidenceSection(d);
        this.handleCartaPoderSection(d);

        this.modals.viewDetails.show();
    },

    handleSignatureSection: function(d) {
        const section = document.getElementById('view_charge_signature_section');
        const content = document.getElementById('view_charge_signature_content');
        const info = document.getElementById('view_charge_signer_info');

        if (d.hasSignature === '1') {
            section.style.display = 'block';
            info.innerHTML = `<span class="material-symbols-outlined fs-6 align-middle me-1">person</span> ` + 
                (d.titularidad === '1' ? `Titular: <strong>${d.titularName}</strong>` : `Firmado por: <strong>${d.parentesco || d.signer}</strong>`);
            this.load(d.signatureUrl, content, true);
        } else {
            section.style.display = 'none';
        }
    },

    handleEvidenceSection: function(d) {
        const section = document.getElementById('view_charge_evidence_section');
        const content = document.getElementById('view_charge_evidence_content');

        if (d.hasEvidence === '1') {
            section.style.display = 'block';
            this.load(d.evidenceUrl, content, false);
        } else {
            section.style.display = 'none';
        }
    },

    handleCartaPoderSection: function(d) {
        const section = document.getElementById('view_charge_carta_poder_section');
        const content = document.getElementById('view_charge_carta_poder_content');
        const link = document.getElementById('view_charge_carta_poder_link');

        if (d.hasCartaPoder === '1') {
            section.style.display = 'block';
            link.href = d.cartaPoderUrl;
            this.load(d.cartaPoderUrl, content, false);
        } else {
            section.style.display = 'none';
        }
    },

    load: async function(url, container, isSvg = true) {
        container.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary spinner-border-sm"></div></div>';
        try {
            const resp = await fetch(url);
            if (!resp.ok) throw new Error();
            
            if (isSvg) {
                container.innerHTML = await resp.text();
                const svg = container.querySelector('svg');
                if (svg) {
                    svg.setAttribute('width', '100%');
                    svg.setAttribute('height', '120');
                }
            } else {
                const blob = await resp.blob();
                const objUrl = URL.createObjectURL(blob);
                const contentType = resp.headers.get('content-type');
                
                if (contentType?.includes('pdf')) {
                    container.innerHTML = `<div class="p-3 text-muted"><span class="material-symbols-outlined fs-1 d-block mb-2">picture_as_pdf</span> Documento PDF disponible</div>`;
                } else {
                    container.innerHTML = `<img src="${objUrl}" class="img-fluid" style="max-height: 300px; width: auto;">`;
                }
            }
        } catch (e) { 
            console.error(e);
            container.innerHTML = '<p class="text-danger p-3 small mb-0">Error al cargar archivo</p>'; 
        }
    }
};
