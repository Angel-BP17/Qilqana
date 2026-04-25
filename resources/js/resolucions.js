import { ResolucionsManagement } from './modules/resolucions/management';
import { SignatureModule } from './modules/charges/signature';
import { ResolucionViewerModule } from './modules/resolucions/viewer';

export default {
    init: () => {
        ResolucionsManagement.init();
        ResolucionViewerModule.init();

        const signForm = document.getElementById('signChargeForm');
        if (signForm) {
            SignatureModule.init();
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-sign-resolution');
                if (!btn) return;
                try {
                    signForm.action = btn.dataset.action;
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
                } catch (error) { console.error('[Resolucions] Error:', error); }
            });
        }
    }
};
