@if (Auth::user()?->hasRole('ADMINISTRADOR'))
    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-charge"
        data-action="{{ route('charges.destroy', $charge) }}" title="Eliminar" @disabled($disabled ?? false)>
        <i class="fa-solid fa-trash"></i>
    </button>
@endif
