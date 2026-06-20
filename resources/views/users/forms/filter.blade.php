<form class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-md-end" action="{{ route('users.index') }}" method="GET">
    <div class="flex-grow-1" style="min-width: 200px; max-width: 400px;">
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <span class="material-symbols-outlined text-muted">search</span>
            </span>
            <input type="text" class="form-control border-start-0" name="search"
                placeholder="Nombre, correo, DNI" value="{{ request('search') }}">
        </div>
    </div>
    <div style="width: 120px;">
        <select name="role_id" id="role_id" class="form-select">
            <option value="">Rol</option>
            @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @selected(request('role_id') == $rol->id)>
                    {{ $rol->name }}
                </option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary" type="submit">
        <span class="material-symbols-outlined">filter_alt</span>
        <span class="d-md-none d-lg-inline">Filtrar</span>
    </button>
</form>
