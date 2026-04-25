import { LookupModule } from './modules/charges/lookup';
import { FormModule } from './modules/charges/forms';
import { SignatureModule } from './modules/charges/signature';
import { ViewerModule } from './modules/charges/viewer';
import { DashboardModule } from './modules/charges/dashboard';

export default {
    init: () => {
        // Inicializar componentes
        LookupModule.init();
        FormModule.init();
        SignatureModule.init();
        ViewerModule.init();
        
        // Inicializar el orquestador del dashboard
        DashboardModule.init();
    }
};
