import "bootstrap";
import "./bootstrap";
import * as bootstrap from "bootstrap";
import SignaturePad from "signature_pad";
import { runResilient } from './modules/common/resilience';

// Globales
window.bootstrap = bootstrap;
window.SignaturePad = SignaturePad;

/**
 * Orquestador de Módulos Frontend
 */
const orchestrateModules = async () => {
    const pageName = document.body.getAttribute('data-page');
    if (!pageName) return;

    // Mapa de módulos
    const routes = {
        'charges.index': () => import('./charges.js'),
        'resolucions.index': () => import('./resolucions.js'),
        'users.index': () => import('./users.js'),
        'roles.index': () => import('./roles.js'),
        'natural-people.index': () => import('./natural-people.js'),
        'legal-entities.index': () => import('./legal-entities.js'),
        'interesados.index': () => import('./interesados.js'),
        'entities.index': () => import('./entities.js'),
        'login': () => import('./auth-login.js'),
        'activity-logs.index': () => import('./activity-logs.js'),
    };

    if (routes[pageName]) {
        try {
            // Importar dinámicamente
            const module = await routes[pageName]();
            
            // Ejecutar inicialización de forma resiliente
            runResilient(() => {
                // Si el módulo exporta un objeto con init()
                if (module.default && typeof module.default.init === 'function') {
                    module.default.init();
                } 
                // O si es un módulo autoejecutable (estilo antiguo)
                else if (typeof module.init === 'function') {
                    module.init();
                }
            });
        } catch (error) {
            console.error(`[Orchestrator] Falló la carga del módulo '${pageName}':`, error);
        }
    }
};

// Iniciar orquestación
orchestrateModules();
