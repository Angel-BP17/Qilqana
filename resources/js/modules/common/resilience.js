/**
 * Ejecuta un callback de forma segura asegurando que el DOM esté listo.
 * @param {Function} callback 
 */
export const runResilient = (callback) => {
    if (typeof callback !== 'function') return;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        // El DOM ya está listo o cargado, ejecutar inmediatamente
        callback();
    }
};

/**
 * Intenta obtener una instancia de Bootstrap de forma segura.
 */
export const getBootstrapInstance = (element, type) => {
    const bootstrapGlobal = window.bootstrap || bootstrap;
    if (!bootstrapGlobal || !bootstrapGlobal[type]) return null;
    
    return bootstrapGlobal[type].getInstance(element) || new bootstrapGlobal[type](element);
};
