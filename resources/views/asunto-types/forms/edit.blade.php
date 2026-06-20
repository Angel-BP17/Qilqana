<div class="modal fade" id="editAsuntoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar tipo de asunto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="editAsuntoForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_asunto_name" class="form-label fw-bold">Nombre del Asunto</label>
                        <input type="text" class="form-control text-uppercase" name="name" id="edit_asunto_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_resolucion_types" class="form-label fw-bold">Tipos de Resolución Compatibles</label>
                        <select class="form-select select2-resolucion-types-edit" name="resolucion_type_ids[]" id="edit_resolucion_types" multiple required data-placeholder="Seleccione los tipos compatibles...">
                            @foreach($resolucionTypes as $rt)
                                <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_asunto_description" class="form-label fw-bold">Descripción (Opcional)</label>
                        <textarea class="form-control" name="description" id="edit_asunto_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <span class="material-symbols-outlined align-middle me-1">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
