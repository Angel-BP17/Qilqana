<div class="modal fade" id="editChargeModal" tabindex="-1" aria-labelledby="editChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editChargeModalLabel">Editar cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editChargeForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_tipo_interesado" class="form-label">Tipo de interesado</label>
                            <select class="form-select" id="edit_tipo_interesado" name="tipo_interesado" required>
                                <option value="">Seleccione</option>
                                <option value="Persona Juridica">Persona Juridica</option>
                                <option value="Persona Natural">Persona Natural</option>
                                <option value="Trabajador UGEL">Trabajador UGEL</option>
                            </select>
                        </div>
                        <div class="col-md-6 assigned-user-field-edit d-none">
                            <label for="edit_assigned_to" class="form-label">Enviar a</label>
                            <select class="form-select select2-user" id="edit_assigned_to" name="assigned_to">
                                <option value="">No enviar (quedara sin asignar)</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1 persona-juridica-fields-edit d-none">
                        <div class="col-md-6">
                            <label for="edit_ruc" class="form-label">RUC</label>
                            <div class="input-group">
                                <input type="text" class="form-control input-lookup-special" id="edit_ruc" name="ruc">
                                <button class="btn btn-lookup-special" type="button" id="lookup_charge_ruc_btn_edit">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_razon_social" class="form-label">Razon social</label>
                            <input type="text" class="form-control" id="edit_razon_social" name="razon_social"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_district" class="form-label">Distrito</label>
                            <input type="text" class="form-control" id="edit_district" name="district"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_representative_dni" class="form-label">DNI representante</label>
                            <div class="input-group">
                                <input type="text" class="form-control input-lookup-special" id="edit_representative_dni" name="representative_dni" maxlength="10">
                                <button class="btn btn-lookup-special" type="button" id="lookup_representative_dni_btn_edit">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_representative_nombres" class="form-label">Nombres representante</label>
                            <input type="text" class="form-control" id="edit_representative_nombres" name="representative_nombres"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_representative_apellido_paterno" class="form-label">Apellido paterno rep.</label>
                            <input type="text" class="form-control" id="edit_representative_apellido_paterno" name="representative_apellido_paterno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_representative_apellido_materno" class="form-label">Apellido materno rep.</label>
                            <input type="text" class="form-control" id="edit_representative_apellido_materno" name="representative_apellido_materno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_representative_cargo" class="form-label">Cargo representante</label>
                            <input type="text" class="form-control" id="edit_representative_cargo" name="representative_cargo"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_representative_since" class="form-label">Representante desde</label>
                            <input type="date" class="form-control" id="edit_representative_since" name="representative_since">
                        </div>
                    </div>
                    <div class="row g-3 mt-1 persona-natural-fields-edit d-none">
                        <div class="col-md-3">
                            <label for="edit_dni" class="form-label">DNI</label>
                            <div class="input-group">
                                <input type="text" class="form-control input-lookup-special" id="edit_dni" name="dni" minlength="8" maxlength="10"
                                    inputmode="numeric" pattern="\d{8,10}">
                                <button class="btn btn-lookup-special" type="button" id="lookup_charge_dni_btn_edit">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_nombres" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="edit_nombres" name="nombres"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_apellido_paterno" class="form-label">Apellido paterno</label>
                            <input type="text" class="form-control" id="edit_apellido_paterno" name="apellido_paterno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_apellido_materno" class="form-label">Apellido materno</label>
                            <input type="text" class="form-control" id="edit_apellido_materno" name="apellido_materno"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col">
                            <label for="edit_asunto" class="form-label">Se remite</label>
                            <input type="text" class="form-control" id="edit_asunto" name="asunto"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_document_date" class="form-label">Fecha del documento</label>
                            <input type="date" class="form-control" id="edit_document_date" name="document_date">
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
