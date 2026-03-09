<form id="createResolutionForm" method="POST" action="{{ route('resolucions.store') }}">
    @csrf
    <!-- Formulario de creacion separado para reutilizar en modal -->
    <div class="row">
        <div class="col-md">
            <div class="input-group mb-3">
                <span class="input-group-text" id="create_rd">
                    <i class="fa-solid fa-hashtag"></i>
                </span>
                <input type="text" class="form-control" name="rd" id="create_resolution_rd"
                    placeholder="Numero de resolucion" aria-describedby="create_rd"
                    value="{{ old('rd') }}" required>
                <input type="date" class="form-control" name="fecha" id="create_resolution_fecha"
                    placeholder="Fecha de la resolucion" value="{{ old('date') }}" required>
                <span class="input-group-text" id="create_dni">
                    <i class="bi bi-person-vcard-fill"></i>
                </span>
                <input type="text" class="form-control" name="dni" id="create_resolution_dni" placeholder="DNI"
                    aria-describedby="create_dni" value="{{ old('dni') }}" required minlength="8" maxlength="10"
                    inputmode="numeric" pattern="\d{8,10}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text" id="create_nombres_apellidos">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input type="text" class="form-control" name="nombres_apellidos" id="create_resolution_nombres"
                    placeholder="Nombres y apellidos" aria-describedby="create_nombres_apellidos"
                    value="{{ old('nombres_apellidos') }}" required>
                <span class="input-group-text" id="create_procedencia">
                    <i class="fa-solid fa-building"></i>
                </span>
                <input type="text" class="form-control" name="procedencia" id="create_resolution_procedencia"
                    placeholder="Procedencia" aria-describedby="create_procedencia"
                    value="{{ old('procedencia') }}">
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text">Asunto</span>
                <textarea class="form-control" name="asunto" id="create_resolution_asunto" rows="3" required>{{ old('asunto') }}</textarea>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-pen-to-square"></i> Enviar
        </button>
    </div>
</form>
