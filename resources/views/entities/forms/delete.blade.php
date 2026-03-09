<button type="button" class="btn btn-outline-danger btn-sm btn-delete-entity"
    data-action="{{ route('entities.destroy', $entity) }}"
    title="{{ ($disabled ?? false) ? 'No tienes permiso para eliminar entidades' : 'Eliminar' }}"
    @disabled($disabled ?? false)>
    <i class="fa-solid fa-trash"></i>
</button>




