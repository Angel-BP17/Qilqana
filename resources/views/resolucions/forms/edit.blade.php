@php
    $formAction = $action ?? '#';
    $resolutionData = $resolucion ?? null;
    $fechaValue = $resolutionData?->fecha ? $resolutionData->fecha->format('Y-m-d') : '';
@endphp

<form id="editResolutionForm" method="POST" action="{{ $formAction }}">
    @csrf
    @method('PUT')
    <!-- Formulario de edicion separado para reutilizar en modal -->
    <div class="row">
        <div class="col-md">
            <div class="input-group mb-3">
                <span class="input-group-text" id="edit_rd">
                    <i class="fa-solid fa-hashtag"></i>
                </span>
                <input type="text" class="form-control" name="rd" id="edit_resolution_rd"
                    placeholder="Numero de resolucion" aria-describedby="edit_rd"
                    value="{{ old('rd', $resolutionData?->rd ?? '') }}" required>
                <input type="date" class="form-control" name="fecha" id="edit_resolution_fecha"
                    placeholder="Fecha de la resolucion" value="{{ old('fecha', $fechaValue) }}" required>
                <span class="input-group-text" id="edit_dni">N&deg;</span>
                <input type="text" class="form-control" name="dni" id="edit_resolution_dni" placeholder="DNI"
                    aria-describedby="edit_dni" value="{{ old('dni', $resolutionData?->dni ?? '') }}" required
                    minlength="8" maxlength="10" inputmode="numeric" pattern="\d{8,10}">
            </div>
        </div>
        <div class="col-md">
            <div class="input-group mb-3">
                <span class="input-group-text" id="edit_nombres_apellidos">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input type="text" class="form-control" name="nombres_apellidos" id="edit_resolution_nombres"
                    placeholder="Nombres y apellidos" aria-describedby="edit_nombres_apellidos"
                    value="{{ old('nombres_apellidos', $resolutionData?->nombres_apellidos ?? '') }}" required>
                <span class="input-group-text" id="edit_procedencia">
                    <i class="fa-solid fa-building"></i>
                </span>
                <input type="text" class="form-control" name="procedencia" id="edit_resolution_procedencia"
                    placeholder="Procedencia" aria-describedby="edit_procedencia"
                    value="{{ old('procedencia', $resolutionData?->procedencia ?? '') }}">
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text">Asunto</span>
                <textarea class="form-control" name="asunto" id="edit_resolution_asunto" rows="3" required>{{ old('asunto', $resolutionData?->asunto ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-paper-plane"></i> Guardar
        </button>
    </div>
</form>
