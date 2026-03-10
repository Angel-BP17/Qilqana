export const ImportHelper = {
    setup: (inputId, buttonId) => {
        const input = document.querySelector(`[data-import-input="${inputId}"]`);
        const btn = document.getElementById(buttonId);
        if (!input || !btn) return;

        const toggle = () => {
            btn.disabled = !input.files || input.files.length === 0;
        };
        toggle();
        input.addEventListener('change', toggle);
    }
};
