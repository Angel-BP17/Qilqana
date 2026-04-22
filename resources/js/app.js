import "bootstrap";
import "./bootstrap";
import * as bootstrap from "bootstrap";
import SignaturePad from "signature_pad";

// Hacer librerías disponibles globalmente
window.bootstrap = bootstrap;
window.SignaturePad = SignaturePad;

/**
 * Sistema de Carga Dinámica de Módulos
 * Esto evita tener que declarar múltiples entry points en Vite
 * y mejora el rendimiento al cargar solo el código necesario.
 */
document.addEventListener('DOMContentLoaded', async () => {
    const pageName = document.body.getAttribute('data-page');

    if (!pageName) return;

    // Mapa de rutas a archivos JS (relativo a resources/js/)
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
            await routes[pageName]();
            // console.debug(`[Vite] Módulo '${pageName}' cargado dinámicamente.`);
        } catch (error) {
            console.error(`[Vite] Error al cargar el módulo '${pageName}':`, error);
        }
    }
});
