<div class="modal fade" id="deleteAsuntoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Eliminar tipo de asunto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="deleteAsuntoForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center">
                    <span class="material-symbols-outlined text-danger mb-3" style="font-size: 4rem;">warning</span>
                    <p class="mb-0">¿Estás seguro de que deseas eliminar este tipo de asunto?</p>
                    <small class="text-muted">Esta acción no se puede deshacer y no procederá si hay resoluciones usando este asunto.</small>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">Sí, eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>
