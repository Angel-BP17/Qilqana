import { ResolucionsManagement } from './modules/resolucions/management';
import { SignatureModule } from './modules/charges/signature';
import { ResolucionViewerModule } from './modules/resolucions/viewer';
import { FormModule } from './modules/charges/forms';
import { LookupModule } from './modules/charges/lookup';

export default {
    init: () => {
        ResolucionsManagement.init();
        ResolucionViewerModule.init();
        FormModule.init();
        LookupModule.init();

        const signForm = document.getElementById('signChargeForm');
        if (signForm) {
            SignatureModule.init();
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-sign-resolution');
                if (!btn) return;
                try {
                    const charges = JSON.parse(btn.dataset.charges || '[]');
                    
                    const openSignModal = (actionUrl) => {
                        signForm.action = actionUrl;
                        const externalFields = document.getElementById('sign_external_fields');
                        if (externalFields) {
                            externalFields.classList.remove('d-none');
                            const titularYes = document.getElementById('sign_titularidad_yes');
                            if (titularYes) {
                                titularYes.checked = true;
                                titularYes.dispatchEvent(new Event('change'));
                            }
                        }
                        const modalElement = document.getElementById('signChargeModal');
                        if (modalElement) {
                            const bootstrapInstance = window.bootstrap || bootstrap;
                            let modal = bootstrapInstance.Modal.getOrCreateInstance(modalElement);
                            modal.show();
                            setTimeout(() => SignatureModule.resize(), 300);
                        }
                    };

                    if (charges.length === 1) {
                        // Solo hay uno, abrir directamente el panel de firma
                        openSignModal(charges[0].action_url);
                    } else if (charges.length > 1) {
                        // Hay más de uno, mostrar modal intermedia
                        const selectModalElement = document.getElementById('selectSigneeModal');
                        const listGroup = document.getElementById('select_signee_list');
                        if (selectModalElement && listGroup) {
                            listGroup.innerHTML = '';
                            charges.forEach(c => {
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3';
                                item.innerHTML = `
                                    <div class="d-flex flex-column text-start">
                                        <strong class="text-dark">${c.interesado_name}</strong>
                                        <small class="text-muted small" style="font-size: 0.85em;">${c.interesado_type}</small>
                                    </div>
                                    <span class="material-symbols-outlined text-success">edit_square</span>
                                `;
                                item.onclick = () => {
                                    const bootstrapInstance = window.bootstrap || bootstrap;
                                    const selectModal = bootstrapInstance.Modal.getOrCreateInstance(selectModalElement);
                                    selectModal.hide();
                                    openSignModal(c.action_url);
                                };
                                listGroup.appendChild(item);
                            });

                            const bootstrapInstance = window.bootstrap || bootstrap;
                            const selectModal = bootstrapInstance.Modal.getOrCreateInstance(selectModalElement);
                            selectModal.show();
                        }
                    } else {
                        // En caso de retrocompatibilidad (si no se pasó data-charges, usar data-action antigua)
                        if (btn.dataset.action) {
                            openSignModal(btn.dataset.action);
                        }
                    }
                } catch (error) { console.error('[Resolucions] Error:', error); }
            });
        }
    }
};
