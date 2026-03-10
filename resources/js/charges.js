import { LookupModule } from './modules/charges/lookup';
import { FormModule } from './modules/charges/forms';
import { SignatureModule } from './modules/charges/signature';
import { ViewerModule } from './modules/charges/viewer';
import { DashboardModule } from './modules/charges/dashboard';

/**
 * Punto de entrada para el módulo de Cargos.
 * Inicializa todos los sub-módulos desacoplados.
 */
const init = () => {
    // Inicializar módulos independientes
    LookupModule.init();
    FormModule.init();
    SignatureModule.init();
    ViewerModule.init();
    
    // Inicializar el orquestador del dashboard
    DashboardModule.init();
};

// Inicialización resiliente
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
