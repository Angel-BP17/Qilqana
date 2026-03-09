<div class="modal fade" id="createChargeModal" tabindex="-1" aria-labelledby="createChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="createChargeModalLabel">Registrar cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('charges.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipo_interesado" class="form-label">Tipo de interesado</label>
                            <select class="form-select" id="tipo_interesado" name="tipo_interesado" required>
                                <option value="">Seleccione</option>
                                <option value="Persona Juridica" @selected(old('tipo_interesado') === 'Persona Juridica')>
                                    Persona Juridica</option>
                                <option value="Persona Natural" @selected(old('tipo_interesado') === 'Persona Natural')>
                                    Persona Natural</option>
                                <option value="Trabajador UGEL" @selected(old('tipo_interesado') === 'Trabajador UGEL')>
                                    Trabajador UGEL</option>
                            </select>
                        </div>
                        <div class="col-md-6 assigned-user-field d-none">
                            <label for="assigned_to" class="form-label">Enviar a</label>
                            <select class="form-select select2-user" id="assigned_to" name="assigned_to">
                                <option value="">No enviar (quedara sin asignar)</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1 persona-juridica-fields d-none">
                        <div class="col-md-6">
                            <label for="ruc" class="form-label">RUC</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ruc" name="ruc"
                                    value="{{ old('ruc') }}">
                                <button class="btn btn-outline-secondary" type="button" id="lookup_charge_ruc_btn">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="razon_social" class="form-label">Razon social</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social"
                                value="{{ old('razon_social') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="district" class="form-label">Distrito</label>
                            <input type="text" class="form-control" id="district" name="district"
                                value="{{ old('district') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="representative_dni" class="form-label">DNI representante</label>
                            <input type="text" class="form-control" id="representative_dni" name="representative_dni"
                                value="{{ old('representative_dni') }}" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label for="representative_name" class="form-label">Nombre representante</label>
                            <input type="text" class="form-control" id="representative_name" name="representative_name"
                                value="{{ old('representative_name') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="representative_cargo" class="form-label">Cargo representante</label>
                            <input type="text" class="form-control" id="representative_cargo" name="representative_cargo"
                                value="{{ old('representative_cargo') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="representative_since" class="form-label">Representante desde</label>
                            <input type="date" class="form-control" id="representative_since" name="representative_since"
                                value="{{ old('representative_since') }}">
                        </div>
                    </div>
                    <div class="row g-3 mt-1 persona-natural-fields d-none">
                        <div class="col-md-3">
                            <label for="dni" class="form-label">DNI</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="dni" name="dni"
                                    value="{{ old('dni') }}" minlength="8" maxlength="10" inputmode="numeric"
                                    pattern="\d{8,10}">
                                <button class="btn btn-outline-secondary" type="button" id="lookup_charge_dni_btn">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="nombres" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="nombres" name="nombres"
                                value="{{ old('nombres') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-3">
                            <label for="apellido_paterno" class="form-label">Apellido paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno"
                                value="{{ old('apellido_paterno') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-3">
                            <label for="apellido_materno" class="form-label">Apellido materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                                value="{{ old('apellido_materno') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col">
                            <label for="asunto" class="form-label">Se remite</label>
                            <input type="text" class="form-control" id="asunto" name="asunto"
                                value="{{ old('asunto') }}" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" required>
                        </div>
                        <div class="col-md-4">
                            <label for="document_date" class="form-label">Fecha del documento</label>
                            <input type="date" class="form-control" id="document_date" name="document_date"
                                value="{{ old('document_date') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
