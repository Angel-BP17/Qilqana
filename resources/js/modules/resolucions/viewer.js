import { ViewerModule as SharedViewer } from '../charges/viewer';

export const ResolucionViewerModule = {
    modals: {},
    init: function() {
        const detailsEl = document.getElementById('viewResolucionDetailsModal');
        if (detailsEl) {
            this.modals.viewDetails = new bootstrap.Modal(detailsEl);
            
            // Limpiar iframe al cerrar el modal para liberar memoria
            detailsEl.addEventListener('hidden.bs.modal', () => {
                const resPdfIframe = document.getElementById('view_res_pdf_iframe');
                if (resPdfIframe) {
                    resPdfIframe.src = '';
                }
            });

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
        document.getElementById('view_res_resolucion_type').textContent = d.resolucionType || '---';
        document.getElementById('view_res_asunto_type').textContent = d.asuntoType || '---';
        document.getElementById('view_res_asunto').textContent = d.asunto;
        document.getElementById('view_res_fecha').textContent = d.fecha;
        document.getElementById('view_res_periodo').textContent = d.periodo;
        document.getElementById('view_res_procedencia').textContent = d.procedencia || '---';

        // Documento de la Resolución (PDF)
        const resDocWrapper = document.getElementById('view_res_document_wrapper');
        const resPdfLink = document.getElementById('view_res_pdf_link');
        const resPdfIframe = document.getElementById('view_res_pdf_iframe');
        if (resDocWrapper && resPdfLink && resPdfIframe) {
            if (d.resDocumentUrl) {
                resDocWrapper.classList.remove('d-none');
                resPdfLink.href = d.resDocumentUrl;
                resPdfIframe.src = d.resDocumentUrl;
            } else {
                resDocWrapper.classList.add('d-none');
                resPdfLink.href = '#';
                resPdfIframe.src = '';
            }
        }

        // Gestión de Información del Cargo
        const chargeInfo = document.getElementById('view_res_charge_info');
        const noChargeAlert = document.getElementById('view_res_no_charge_alert');

        if (d.hasCharge === '1' && (d.hasSignature === '1' || d.hasEvidence === '1' || d.hasDocument === '1')) {
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
        // Documento del Cargo
        const docSection = document.getElementById('view_res_document_section');
        const docLink = document.getElementById('view_res_document_link');
        if (d.hasDocument === '1') {
            docSection.style.display = 'block';
            docLink.href = d.documentUrl;
        } else {
            docSection.style.display = 'none';
        }

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
        const mapLink = document.getElementById('view_res_map_link');

        if (d.hasEvidence === '1') {
            evSection.style.display = 'block';
            SharedViewer.load(d.evidenceUrl, evContent, false);

            if (d.evidenceLocation && d.evidenceLocation !== 'null') {
                try {
                    const loc = typeof d.evidenceLocation === 'string' ? JSON.parse(d.evidenceLocation) : d.evidenceLocation;
                    if (loc && loc.lat && loc.lng) {
                        mapLink.href = `https://www.google.com/maps/search/?api=1&query=${loc.lat},${loc.lng}`;
                        mapLink.classList.remove('d-none');
                    } else {
                        mapLink.classList.add('d-none');
                    }
                } catch (e) { mapLink.classList.add('d-none'); }
            } else {
                mapLink.classList.add('d-none');
            }
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
