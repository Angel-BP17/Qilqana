@if (Auth::user()?->hasRole('ADMINISTRADOR'))
    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-charge"
        data-action="{{ route('charges.destroy', $charge->id) }}" title="Eliminar" @disabled($disabled ?? false)>
        <span class="material-symbols-outlined">delete</span>
    </button>
@endif
