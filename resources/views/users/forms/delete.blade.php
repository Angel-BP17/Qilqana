<button type="button" class="btn btn-outline-danger btn-sm btn-delete-user"
    data-action="{{ route('users.destroy', $user) }}"
    title="{{ ($disabled ?? false) ? 'No tienes permiso para eliminar usuarios' : 'Eliminar' }}"
    @disabled($disabled ?? false)>
    <span class="material-symbols-outlined">delete</span>
</button>
