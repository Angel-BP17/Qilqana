<form id="createResolutionForm" method="POST" action="{{ route('resolucions.store') }}">
    @csrf
    <!-- Formulario de creacion separado para reutilizar en modal -->
    <div class="row">
        <div class="col-md-8">
            <div class="input-group mb-3">
                <span class="input-group-text" id="create_rd">
                    <span class="material-symbols-outlined">tag</span>
                </span>
                <input type="text" class="form-control" name="rd" id="create_resolution_rd"
                    placeholder="Numero de resolucion" aria-describedby="create_rd"
                    value="{{ old('rd') }}" required>
                <span class="input-group-text">
                    <span class="material-symbols-outlined">calendar_today</span>
                </span>
                <input type="date" class="form-control" name="fecha" id="create_resolution_fecha"
                    placeholder="Fecha de la resolucion" value="{{ old('date') }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group mb-3">
                <span class="input-group-text" id="create_dni">
                    <span class="material-symbols-outlined">badge</span>
                </span>
                <input type="text" class="form-control" name="dni" id="create_resolution_dni" placeholder="DNI"
                    aria-describedby="create_dni" value="{{ old('dni') }}" required minlength="8" maxlength="10"
                    inputmode="numeric" pattern="\d{8,10}">
                <button class="btn btn-outline-primary" type="button" id="lookup_dni_btn_resolutions">Buscar</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label small text-muted">Nombres</label>
                <div class="input-group">
                    <span class="input-group-text"><span class="material-symbols-outlined">person</span></span>
                    <input type="text" class="form-control" name="nombres" id="create_resolution_nombres" placeholder="Nombres" required>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label small text-muted">Apellido Paterno</label>
                <input type="text" class="form-control" name="apellido_paterno" id="create_resolution_apellido_paterno" placeholder="Apellido Paterno" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label small text-muted">Apellido Materno</label>
                <input type="text" class="form-control" name="apellido_materno" id="create_resolution_apellido_materno" placeholder="Apellido Materno" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="input-group mb-3">
                <span class="input-group-text" id="create_procedencia">
                    <span class="material-symbols-outlined">corporate_fare</span>
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
            <span class="material-symbols-outlined">edit</span> Enviar
        </button>
    </div>
</form>
