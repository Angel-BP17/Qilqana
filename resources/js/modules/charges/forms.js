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
            const isNatural = val === 'Persona Natural' || val === 'Trabajador UGEL';
            const isJuridica = val === 'Persona Juridica';
            const isUgel = val === 'Trabajador UGEL';

            document.querySelector(prefix === 'edit' ? '.persona-natural-fields-edit' : '.persona-natural-fields')?.classList.toggle('d-none', !isNatural);
            document.querySelector(prefix === 'edit' ? '.persona-juridica-fields-edit' : '.persona-juridica-fields')?.classList.toggle('d-none', !isJuridica);
            document.querySelector(prefix === 'edit' ? '.assigned-user-field-edit' : '.assigned-user-field')?.classList.toggle('d-none', !isUgel);
        };
        select.addEventListener('change', toggle);
        toggle();
    }
};
