import { ResolucionsManagement } from './modules/resolucions/management';
import { SignatureModule } from './modules/charges/signature';

$(document).ready(() => {
    ResolucionsManagement.init();
});

// Lógica de firmas para resoluciones
document.addEventListener('DOMContentLoaded', () => {
    const signForm = document.getElementById('signChargeForm');
    if (signForm) {
        SignatureModule.init();
        
        // Vincular botones de firma de resoluciones
        document.querySelectorAll('.btn-sign-resolution').forEach(btn => {
            btn.onclick = () => {
                signForm.action = btn.dataset.action;
                const modal = new bootstrap.Modal(document.getElementById('signChargeModal'));
                modal.show();
                setTimeout(() => SignatureModule.resize(), 300);
            };
        });
    }
});
