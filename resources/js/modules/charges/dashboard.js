import { SignatureModule } from './signature';
import { ViewerModule } from './viewer';

export const DashboardModule = {
    config: {
        tabStorageKey: 'charges.activeTab',
        interaction: { active: false, timeout: null }
    },

    init: function() {
        const dashboardEl = document.getElementById('charges-dashboard');
        if (!dashboardEl) return;

        this.bindEvents(dashboardEl);
        this.restoreTab();
        this.setupAutoRefresh(dashboardEl);
        this.listenInteractions(dashboardEl);
    },

    bindEvents: function(container) {
        // Ver Firma
        container.querySelectorAll('.btn-signature-view').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                const d = btn.dataset;
                const signerEl = document.getElementById('viewSignatureSigner');
                signerEl.textContent = d.titularidad === '1' ? `Titular: ${d.titularName}` : `Firmado por: ${d.parentesco || d.signer}`;
                signerEl.style.display = 'block';
                if (d.evidence) ViewerModule.load(d.evidence, document.getElementById('viewSignatureExtra'), false);
                else document.getElementById('viewSignatureExtra').style.display = 'none';
                ViewerModule.load(d.url, document.getElementById('viewSignatureContent'), true);
                ViewerModule.modals.viewSign.show();
            };
        });

        // Ver Carta Poder
        container.querySelectorAll('.btn-carta-poder-view').forEach(btn => {
            btn.onclick = () => {
                ViewerModule.load(btn.dataset.url, document.getElementById('viewCartaPoderContent'), false);
                ViewerModule.modals.viewCarta.show();
            };
        });

        // Abrir Modal Firma
        container.querySelectorAll('.btn-sign-charge').forEach(btn => {
            btn.onclick = () => {
                const form = document.getElementById('signChargeForm');
                form.action = btn.dataset.action;
                const chargeData = JSON.parse(btn.dataset.charge);
                
                // Mostrar campos de titularidad solo para externos
                const isExternal = ['Persona Natural', 'Persona Juridica'].includes(chargeData.tipo_interesado);
                const externalFields = document.getElementById('sign_external_fields');
                if (externalFields) {
                    externalFields.classList.toggle('d-none', !isExternal);
                    // Resetear a "Soy titular" por defecto
                    const titularYes = document.getElementById('sign_titularidad_yes');
                    if (titularYes) {
                        titularYes.checked = true;
                        titularYes.dispatchEvent(new Event('change'));
                    }
                }

                new bootstrap.Modal(document.getElementById('signChargeModal')).show();
                setTimeout(() => SignatureModule.resize(), 300);
            };
        });

        // Abrir Modal Editar
        container.querySelectorAll('.btn-edit-charge').forEach(btn => {
            btn.onclick = () => {
                const charge = JSON.parse(btn.dataset.charge);
                const form = document.getElementById('editChargeForm');
                form.action = btn.dataset.action;
                document.getElementById('edit_asunto').value = charge.asunto || '';
                document.getElementById('edit_document_date').value = charge.document_date || '';
                document.getElementById('edit_tipo_interesado').value = charge.tipo_interesado || '';
                
                // Llenar datos específicos si existen
                if (charge.natural_person) {
                    document.getElementById('edit_dni').value = charge.natural_person.dni || '';
                    document.getElementById('edit_nombres').value = charge.natural_person.nombres || '';
                    document.getElementById('edit_apellido_paterno').value = charge.natural_person.apellido_paterno || '';
                    document.getElementById('edit_apellido_materno').value = charge.natural_person.apellido_materno || '';
                }
                if (charge.legal_entity) {
                    document.getElementById('edit_ruc').value = charge.legal_entity.ruc || '';
                    document.getElementById('edit_razon_social').value = charge.legal_entity.razon_social || '';
                    document.getElementById('edit_district').value = charge.legal_entity.district || '';
                    
                    if (charge.legal_entity.representative) {
                        const rep = charge.legal_entity.representative;
                        const p = 'edit_representative_';
                        document.getElementById(p + 'dni').value = rep.natural_person?.dni || '';
                        document.getElementById(p + 'nombres').value = rep.natural_person?.nombres || '';
                        document.getElementById(p + 'apellido_paterno').value = rep.natural_person?.apellido_paterno || '';
                        document.getElementById(p + 'apellido_materno').value = rep.natural_person?.apellido_materno || '';
                        document.getElementById('edit_representative_cargo').value = rep.cargo || '';
                        document.getElementById('edit_representative_since').value = rep.fecha_desde || '';
                    }
                }

                document.getElementById('edit_tipo_interesado').dispatchEvent(new Event('change'));
                new bootstrap.Modal(document.getElementById('editChargeModal')).show();
            };
        });

        // Rechazar / Eliminar
        container.querySelectorAll('.btn-reject-charge').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('rejectChargeForm').action = btn.dataset.action;
                new bootstrap.Modal(document.getElementById('rejectChargeModal')).show();
            };
        });
        container.querySelectorAll('.btn-delete-charge').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('deleteChargeForm').action = btn.dataset.action;
                new bootstrap.Modal(document.getElementById('deleteChargeModal')).show();
            };
        });

        // Persistencia de pestañas
        document.querySelectorAll('#charges-tabs .nav-link').forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => localStorage.setItem(this.config.tabStorageKey, e.target.id));
        });
    },

    setupAutoRefresh: function(container) {
        const interval = (Number(container.dataset.refreshInterval) || 5) * 1000;
        setInterval(async () => {
            if (this.config.interaction.active) return;
            const resp = await fetch(container.dataset.refreshUrl, { headers: {'X-Requested-With': 'XMLHttpRequest'} });
            if (resp.ok) {
                const activeId = document.querySelector('#charges-tabs .nav-link.active')?.id;
                container.innerHTML = await resp.text();
                this.bindEvents(container);
                if (activeId) {
                    const btn = document.getElementById(activeId);
                    if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
                }
            }
        }, interval);
    },

    listenInteractions: function(container) {
        ['input', 'change', 'keydown', 'mousedown'].forEach(ev => {
            container.addEventListener(ev, () => {
                this.config.interaction.active = true;
                clearTimeout(this.config.interaction.timeout);
                this.config.interaction.timeout = setTimeout(() => { this.config.interaction.active = false; }, 2000);
            });
        });
    },

    restoreTab: function() {
        const savedTab = localStorage.getItem(this.config.tabStorageKey);
        if (savedTab) {
            const tabBtn = document.getElementById(savedTab);
            if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
        }
    }
};
