<button type="button" class="btn btn-outline-danger btn-sm btn-delete-interesado"
    data-action="{{ route('interesados.destroy', $interesado) }}"
    title="{{ ($disabled ?? false) ? 'No tienes permiso para eliminar interesados' : 'Eliminar' }}"
    @disabled($disabled ?? false)>
    <span class="material-symbols-outlined">delete</span>
</button>
