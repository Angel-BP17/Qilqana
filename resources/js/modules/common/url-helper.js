/**
 * Obtiene la URL base de la aplicación desde el meta tag.
 * Útil para despliegues en subcarpetas de hosting compartido.
 */
export const getBaseUrl = () => {
    const meta = document.querySelector('meta[name="base-url"]');
    return meta ? meta.getAttribute('content').replace(/\/$/, '') : '';
};

/**
 * Genera una URL absoluta interna del proyecto.
 */
export const route = (path) => {
    const cleanPath = path.replace(/^\//, '');
    return `${getBaseUrl()}/${cleanPath}`;
};
