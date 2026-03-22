export const FormModule = {
    init: function() {
        this.setup('create', 'tipo_interesado');
        this.setup('edit', 'edit_tipo_interesado');
    },
    setup: (prefix, selectId) => {
        const select = document.getElementById(selectId);
        if (!select) return;

        const toggle = () => {
            const val = select.value;
            const isNatural = val === 'Persona Natural';
            const isJuridica = val === 'Persona Juridica';
            const isUgel = val === 'Trabajador UGEL';

            // Contenedores principales
            const naturalFields = document.querySelector(prefix === 'edit' ? '.persona-natural-fields-edit' : '.persona-natural-fields');
            const juridicaFields = document.querySelector(prefix === 'edit' ? '.persona-juridica-fields-edit' : '.persona-juridica-fields');
            const assignedFields = document.querySelector(prefix === 'edit' ? '.assigned-user-field-edit' : '.assigned-user-field');

            // Resetear visibilidad de detalles internos al cambiar tipo (solo en creación)
            if (prefix === 'create') {
                document.querySelectorAll('.natural-details, .entity-details, .representative-section, .rep-details').forEach(el => el.classList.add('d-none'));
                document.querySelectorAll('#createChargeForm input:not([name="_token"]):not([name="document_date"])').forEach(i => i.value = '');
            }

            if (naturalFields) naturalFields.classList.toggle('d-none', !isNatural);
            if (juridicaFields) juridicaFields.classList.toggle('d-none', !isJuridica);
            if (assignedFields) assignedFields.classList.toggle('d-none', !isUgel);
        };

        select.addEventListener('change', toggle);
        toggle();
    }
};
