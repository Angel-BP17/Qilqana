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
        // Usar delegación de eventos en el contenedor para todas las acciones
        container.addEventListener('click', (e) => {
            // Ver Detalles de Cargo (Firma, Evidencia, Carta Poder)
            const btnViewDetails = e.target.closest('.btn-view-charge-details');
            if (btnViewDetails) {
                e.preventDefault();
                ViewerModule.showDetails(btnViewDetails.dataset);
                return;
            }

            // Abrir Modal Firma
            const btnSignCharge = e.target.closest('.btn-sign-charge');
            if (btnSignCharge) {
                const form = document.getElementById('signChargeForm');
                form.action = btnSignCharge.dataset.action;
                const chargeData = JSON.parse(btnSignCharge.dataset.charge);
                
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

                const modalEl = document.getElementById('signChargeModal');
                const bootstrapInstance = window.bootstrap || bootstrap;
                let modal = bootstrapInstance.Modal.getInstance(modalEl);
                if (!modal) {
                    modal = new bootstrapInstance.Modal(modalEl);
                }
                modal.show();
                setTimeout(() => SignatureModule.resize(), 300);
                return;
            }

            // Abrir Modal Editar
            const btnEditCharge = e.target.closest('.btn-edit-charge');
            if (btnEditCharge) {
                const charge = JSON.parse(btnEditCharge.dataset.charge);
                const form = document.getElementById('editChargeForm');
                form.action = btnEditCharge.dataset.action;
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
                
                const modalEl = document.getElementById('editChargeModal');
                const bootstrapInstance = window.bootstrap || bootstrap;
                let modal = bootstrapInstance.Modal.getInstance(modalEl);
                if (!modal) {
                    modal = new bootstrapInstance.Modal(modalEl);
                }
                modal.show();
                return;
            }

            // Rechazar
            const btnRejectCharge = e.target.closest('.btn-reject-charge');
            if (btnRejectCharge) {
                document.getElementById('rejectChargeForm').action = btnRejectCharge.dataset.action;
                
                const modalEl = document.getElementById('rejectChargeModal');
                const bootstrapInstance = window.bootstrap || bootstrap;
                let modal = bootstrapInstance.Modal.getInstance(modalEl);
                if (!modal) {
                    modal = new bootstrapInstance.Modal(modalEl);
                }
                modal.show();
                return;
            }

            // Eliminar
            const btnDeleteCharge = e.target.closest('.btn-delete-charge');
            if (btnDeleteCharge) {
                document.getElementById('deleteChargeForm').action = btnDeleteCharge.dataset.action;
                
                const modalEl = document.getElementById('deleteChargeModal');
                const bootstrapInstance = window.bootstrap || bootstrap;
                let modal = bootstrapInstance.Modal.getInstance(modalEl);
                if (!modal) {
                    modal = new bootstrapInstance.Modal(modalEl);
                }
                modal.show();
                return;
            }
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
