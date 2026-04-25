import { ViewerModule as SharedViewer } from '../charges/viewer';

export const ResolucionViewerModule = {
    modals: {},
    init: function() {
        const detailsEl = document.getElementById('viewResolucionDetailsModal');
        if (detailsEl) {
            this.modals.viewDetails = new bootstrap.Modal(detailsEl);
            this.bindEvents();
        }
    },

    bindEvents: function() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-view-res-details');
            if (btn) {
                e.preventDefault();
                this.showDetails(btn.dataset);
            }
        });
    },

    showDetails: function(d) {
        // Información de la Resolución
        document.getElementById('view_res_rd').textContent = d.rd;
        document.getElementById('view_res_interesado').textContent = d.interesado;
        document.getElementById('view_res_dni').textContent = d.dni || '---';
        document.getElementById('view_res_asunto').textContent = d.asunto;
        document.getElementById('view_res_fecha').textContent = d.fecha;
        document.getElementById('view_res_periodo').textContent = d.periodo;
        document.getElementById('view_res_procedencia').textContent = d.procedencia || '---';

        // Gestión de Información del Cargo
        const chargeInfo = document.getElementById('view_res_charge_info');
        const noChargeAlert = document.getElementById('view_res_no_charge_alert');

        if (d.hasCharge === '1' && (d.hasSignature === '1' || d.hasEvidence === '1')) {
            chargeInfo.style.display = 'flex';
            noChargeAlert.style.display = 'none';
            this.handleChargeSections(d);
        } else {
            chargeInfo.style.display = 'none';
            noChargeAlert.style.display = 'block';
        }

        this.modals.viewDetails.show();
    },

    handleChargeSections: function(d) {
        // Reutilizamos la lógica de carga del ViewerModule de cargos
        const sigSection = document.getElementById('view_res_signature_section');
        const sigContent = document.getElementById('view_res_signature_content');
        const sigInfo = document.getElementById('view_res_signer_info');

        if (d.hasSignature === '1') {
            sigSection.style.display = 'block';
            sigInfo.innerHTML = `<span class="material-symbols-outlined fs-6 align-middle me-1">person</span> ` + 
                (d.titularidad === '1' ? `Titular: <strong>${d.interesado}</strong>` : `Firmado por: <strong>${d.parentesco || d.signer}</strong>`);
            SharedViewer.load(d.signatureUrl, sigContent, true);
        } else {
            sigSection.style.display = 'none';
        }

        const evSection = document.getElementById('view_res_evidence_section');
        const evContent = document.getElementById('view_res_evidence_content');
        if (d.hasEvidence === '1') {
            evSection.style.display = 'block';
            SharedViewer.load(d.evidenceUrl, evContent, false);
        } else {
            evSection.style.display = 'none';
        }

        const cpSection = document.getElementById('view_res_carta_poder_section');
        const cpContent = document.getElementById('view_res_carta_poder_content');
        const cpLink = document.getElementById('view_res_carta_poder_link');
        if (d.hasCartaPoder === '1') {
            cpSection.style.display = 'block';
            cpLink.href = d.cartaPoderUrl;
            SharedViewer.load(d.cartaPoderUrl, cpContent, false);
        } else {
            cpSection.style.display = 'none';
        }
    }
};
