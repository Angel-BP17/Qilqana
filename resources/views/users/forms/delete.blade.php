<button type="button" class="{{ $class ?? 'btn btn-outline-danger btn-sm' }} btn-delete-user"
    data-action="{{ route('users.destroy', $user) }}"
    title="{{ ($disabled ?? false) ? 'No tienes permiso para eliminar usuarios' : 'Eliminar' }}"
    @disabled($disabled ?? false)>
    <span class="material-symbols-outlined {{ isset($class) ? 'me-2 fs-5' : '' }}">delete</span>
    @if(isset($class)) Eliminar @endif
</button>
