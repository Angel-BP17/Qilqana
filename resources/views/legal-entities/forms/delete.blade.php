<button type="button" class="btn btn-outline-danger btn-sm btn-delete-legal-entity"
    data-action="{{ route('legal-entities.destroy', $legalEntity) }}"
    title="{{ ($disabled ?? false) ? 'No tienes permiso para eliminar personas juridicas' : 'Eliminar' }}"
    @disabled($disabled ?? false)>
    <span class="material-symbols-outlined">delete</span>
</button>
