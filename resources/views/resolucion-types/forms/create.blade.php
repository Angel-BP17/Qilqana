<div class="modal fade" id="createTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrar tipo de resolución</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('resolucion-types.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nombre del Tipo</label>
                        <input type="text" class="form-control text-uppercase" name="name" id="name" required placeholder="Ej. RESOLUCIÓN DIRECTORAL">
                    </div>
                    <div class="mb-3">
                        <label for="abreviacion" class="form-label fw-bold">Abreviación</label>
                        <input type="text" class="form-control text-uppercase" name="abreviacion" id="abreviacion" placeholder="Ej. RD" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Descripción (Opcional)</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Detalles sobre este tipo de resolución..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <span class="material-symbols-outlined">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
