<form class="d-flex flex-wrap gap-2" action="{{ route('users.index') }}" method="GET">
    <div class="col">
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0" name="search"
                placeholder="Nombre, correo, DNI" value="{{ request('search') }}">
        </div>
    </div>
    <div class="col-2">
        <select name="role_id" id="role_id" class="form-select">
            <option value="">Rol</option>
            @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @selected(request('role_id') == $rol->id)>
                    {{ $rol->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i>
            Filtrar</button>
    </div>
</form>
