<div class="modal fade" id="deleteTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Eliminar tipo de resolución</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="deleteTypeForm" action="#" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center">
                    <span class="material-symbols-outlined text-danger mb-3" style="font-size: 3rem;">warning</span>
                    <p>¿Estás seguro de que deseas eliminar este tipo de resolución?</p>
                    <p class="text-muted small">Esta acción no se puede deshacer y fallará si el tipo tiene resoluciones asociadas.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <span class="material-symbols-outlined">delete</span> Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
