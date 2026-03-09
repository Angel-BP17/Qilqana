document.addEventListener('DOMContentLoaded', () => {
    const toggleFields = (typeValue, juridicaGroup, naturalGroup, cargoGroup) => {
        const showJuridica = typeValue === 'Persona Juridica';
        const showNatural = typeValue === 'Persona Natural';
        const showCargo = showNatural || typeValue === 'Trabajador UGEL';

        if (juridicaGroup) {
            juridicaGroup.classList.toggle('d-none', !showJuridica);
            juridicaGroup.querySelectorAll('input').forEach((input) => {
                input.required = showJuridica;
            });
        }
        if (naturalGroup) {
            naturalGroup.classList.toggle('d-none', !showNatural);
            naturalGroup.querySelectorAll('input').forEach((input) => {
                input.required = showNatural;
            });
        }
        if (cargoGroup) {
            cargoGroup.classList.toggle('d-none', !showCargo);
        }
    };

    const tipoCreate = document.getElementById('tipo_interesado');
    const juridicaCreate = document.querySelector('.persona-juridica-fields');
    const naturalCreate = document.querySelector('.persona-natural-fields');
    const cargoCreate = document.querySelector('.cargo-fields');

    if (tipoCreate) {
        tipoCreate.addEventListener('change', () => {
            toggleFields(tipoCreate.value, juridicaCreate, naturalCreate, cargoCreate);
        });
        toggleFields(tipoCreate.value, juridicaCreate, naturalCreate, cargoCreate);
    }

    const editModalElement = document.getElementById('editInteresadoModal');
    const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
    const editForm = document.getElementById('editInteresadoForm');
    const tipoEdit = document.getElementById('edit_tipo_interesado');
    const juridicaEdit = document.querySelector('.persona-juridica-fields-edit');
    const naturalEdit = document.querySelector('.persona-natural-fields-edit');
    const cargoEdit = document.querySelector('.cargo-fields-edit');

    document.querySelectorAll('.btn-edit-interesado').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            editForm.action = btn.dataset.action || '';
            if (tipoEdit) tipoEdit.value = btn.dataset.tipo || '';
            const setValue = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = value || '';
            };
            setValue('edit_ruc', btn.dataset.ruc);
            setValue('edit_razon_social', btn.dataset.razon);
            setValue('edit_dni', btn.dataset.dni);
            setValue('edit_nombres', btn.dataset.nombres);
            setValue('edit_apellido_paterno', btn.dataset.apellidoPaterno);
            setValue('edit_apellido_materno', btn.dataset.apellidoMaterno);
            setValue('edit_cargo', btn.dataset.cargo);
            toggleFields(tipoEdit?.value || '', juridicaEdit, naturalEdit, cargoEdit);
            editModal?.show();
        });
    });

    if (tipoEdit) {
        tipoEdit.addEventListener('change', () => {
            toggleFields(tipoEdit.value, juridicaEdit, naturalEdit, cargoEdit);
        });
    }

    const deleteModalElement = document.getElementById('deleteInteresadoModal');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteForm = document.getElementById('deleteInteresadoForm');
    const deleteReason = document.getElementById('delete_interesado_reason');

    document.querySelectorAll('.btn-delete-interesado').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!deleteModal || !deleteForm) return;
            deleteForm.action = btn.dataset.action || '';
            if (deleteReason) deleteReason.value = '';
            deleteModal.show();
        });
    });
});
