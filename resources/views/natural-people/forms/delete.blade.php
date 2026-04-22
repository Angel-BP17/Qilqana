<button type="button" class="btn btn-outline-danger btn-sm btn-delete-natural-person"
    data-action="{{ route('natural-people.destroy', $naturalPerson) }}"
    title="{{ ($disabled ?? false) ? 'No tienes permiso para eliminar personas naturales' : 'Eliminar' }}"
    @disabled($disabled ?? false)>
    <span class="material-symbols-outlined">delete</span>
</button>
