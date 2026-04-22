import { ResolucionsManagement } from './modules/resolucions/management';
import { SignatureModule } from './modules/charges/signature';

const initResolucions = () => {
    // Inicializar gestión de resoluciones
    ResolucionsManagement.init();

    // Lógica de firmas para resoluciones
    const signForm = document.getElementById('signChargeForm');
    if (signForm) {
        SignatureModule.init();
        
        console.debug('[Resolucions] Inicializando módulo de firmas');

        // Usar delegación de eventos para que funcione incluso tras recargas dinámicas (búsquedas)
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-sign-resolution');
            if (!btn) return;

            console.debug('[Resolucions] Botón de firma clickeado', btn.dataset.action);

            try {
                signForm.action = btn.dataset.action;
                
                // Las resoluciones siempre son externas (Persona Natural)
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
                if (!modalElement) {
                    console.error('[Resolucions] No se encontró el elemento #signChargeModal');
                    return;
                }

                // Asegurar que usamos el bootstrap global definido en app.js
                const bootstrapInstance = window.bootstrap || bootstrap;
                let modal = bootstrapInstance.Modal.getInstance(modalElement);
                if (!modal) {
                    modal = new bootstrapInstance.Modal(modalElement);
                }
                
                modal.show();
                setTimeout(() => SignatureModule.resize(), 300);
            } catch (error) {
                console.error('[Resolucions] Error al intentar abrir el modal de firma:', error);
            }
        });
    }
};

// Inicialización resiliente: funciona tanto en carga directa como dinámica (Vite)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initResolucions);
} else {
    initResolucions();
}
