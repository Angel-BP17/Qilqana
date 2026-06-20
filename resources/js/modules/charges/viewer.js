export const ViewerModule = {
    modals: {},
    init: function() {
        const detailsEl = document.getElementById('viewChargeDetailsModal');
        if (detailsEl) {
            this.modals.viewDetails = new bootstrap.Modal(detailsEl);
            detailsEl.addEventListener('shown.bs.modal', () => {
                if (this.map) {
                    setTimeout(() => {
                        this.map.invalidateSize();
                    }, 100);
                }
            });
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

        // Gestión de Secciones (Documento, Firma, Evidencia, Carta Poder)
        this.handleDocumentSection(d);
        this.handleSignatureSection(d);
        this.handleEvidenceSection(d);
        this.handleCartaPoderSection(d);

        this.modals.viewDetails.show();
    },

    handleDocumentSection: function(d) {
        const section = document.getElementById('view_charge_document_section');
        const link = document.getElementById('view_charge_document_link');

        if (d.hasDocument === '1') {
            section.style.display = 'block';
            link.href = d.documentUrl;
        } else {
            section.style.display = 'none';
        }
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
        const mapContainer = document.getElementById('view_charge_map_container');
        const mapLink = document.getElementById('view_charge_map_link');
        const btnToggleMap = document.getElementById('btn_toggle_map');

        if (d.hasEvidence === '1') {
            section.style.display = 'block';
            this.load(d.evidenceUrl, content, false);

            if (d.evidenceLocation && d.evidenceLocation !== 'null') {
                try {
                    const loc = typeof d.evidenceLocation === 'string' ? JSON.parse(d.evidenceLocation) : d.evidenceLocation;
                    if (loc && loc.lat && loc.lng) {
                        mapLink.href = `https://www.google.com/maps/search/?api=1&query=${loc.lat},${loc.lng}`;
                        mapLink.classList.remove('d-none');
                        btnToggleMap.classList.remove('d-none');

                        // Limpiar contenedor del mapa
                        if (this.map) {
                            this.map.remove();
                            this.map = null;
                        }

                        // Evento para mostrar/ocultar mapa
                        btnToggleMap.onclick = () => {
                            const isHidden = mapContainer.classList.contains('d-none');
                            mapContainer.classList.toggle('d-none');
                            content.classList.toggle('d-none', isHidden);
                            
                            if (isHidden) {
                                if (!this.map) {
                                    setTimeout(() => {
                                        // Solucionar el problema de los iconos por defecto de Leaflet
                                        delete L.Icon.Default.prototype._getIconUrl;
                                        L.Icon.Default.mergeOptions({
                                            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                                            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                                            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                                        });

                                        this.map = L.map('view_charge_map_container').setView([loc.lat, loc.lng], 16);
                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                            attribution: '&copy; OpenStreetMap'
                                        }).addTo(this.map);
                                        L.marker([loc.lat, loc.lng]).addTo(this.map)
                                            .bindPopup('Lugar de la firma')
                                            .openPopup();
                                        
                                        // Asegurar tamaño correcto en la carga inicial
                                        setTimeout(() => {
                                            if (this.map) this.map.invalidateSize();
                                        }, 100);
                                    }, 200);
                                } else {
                                    // Si el mapa ya existe y se vuelve a mostrar, recalculamos el tamaño
                                    setTimeout(() => {
                                        if (this.map) this.map.invalidateSize();
                                    }, 100);
                                }
                            }
                        };
                    } else {
                        this.hideMapElements(mapLink, btnToggleMap, mapContainer, content);
                    }
                } catch (e) { this.hideMapElements(mapLink, btnToggleMap, mapContainer, content); }
            } else {
                this.hideMapElements(mapLink, btnToggleMap, mapContainer, content);
            }
        } else {
            section.style.display = 'none';
        }
    },

    hideMapElements: function(mapLink, btnToggleMap, mapContainer, content) {
        mapLink.classList.add('d-none');
        btnToggleMap.classList.add('d-none');
        mapContainer.classList.add('d-none');
        content.classList.remove('d-none');
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
