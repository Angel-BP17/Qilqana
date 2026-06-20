<div class="modal fade" id="editTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Editar tipo de resolución</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="editTypeForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label fw-bold">Nombre del Tipo</label>
                        <input type="text" class="form-control text-uppercase" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_abreviacion" class="form-label fw-bold">Abreviación</label>
                        <input type="text" class="form-control text-uppercase" name="abreviacion" id="edit_abreviacion" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label fw-bold">Descripción (Opcional)</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="material-symbols-outlined">save</span> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
