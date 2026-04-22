<button type="button" class="btn btn-outline-danger btn-sm btn-delete-entity"
    data-action="{{ route('entities.destroy', $entity) }}"
    title="{{ ($disabled ?? false) ? 'No tienes permiso para eliminar entidades' : 'Eliminar' }}"
    @disabled($disabled ?? false)>
    <span class="material-symbols-outlined">delete</span>
</button>




