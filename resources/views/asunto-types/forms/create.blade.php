<div class="modal fade" id="createAsuntoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrar tipo de asunto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('asunto-types.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nombre del Asunto</label>
                        <input type="text" class="form-control text-uppercase" name="name" id="name" required placeholder="Ej. LICENCIA POR SALUD">
                    </div>
                    <div class="mb-3">
                        <label for="resolucion_types" class="form-label fw-bold">Tipos de Resolución Compatibles</label>
                        <select class="form-select select2-resolucion-types" name="resolucion_type_ids[]" id="resolucion_types" multiple required data-placeholder="Seleccione los tipos compatibles...">
                            @foreach($resolucionTypes as $rt)
                                <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Descripción (Opcional)</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Detalles sobre este tipo de asunto..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <span class="material-symbols-outlined align-middle me-1">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
