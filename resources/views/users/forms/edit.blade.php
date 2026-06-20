<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editUserModalLabel">Editar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">DNI</label>
                            <div class="input-group">
                                <input type="text" name="dni" id="edit_dni" class="form-control" required maxlength="10">
                                <button class="btn btn-outline-primary" type="button" id="lookup_user_dni_btn_edit">Buscar</button>
                            </div>
                            <div id="edit_user_dni_error" class="text-danger small mt-1 d-none"></div>
                        </div>
                        <div class="col-md-6 d-none edit-user-details-fields">
                            <label class="form-label fw-bold">Nombres</label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();"
                                required>
                        </div>
                        <div class="col-md-6 d-none edit-user-details-fields">
                            <label class="form-label fw-bold">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" id="edit_apellido_paterno" class="form-control"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();"
                                required>
                        </div>
                        <div class="col-md-6 d-none edit-user-details-fields">
                            <label class="form-label fw-bold">Apellido Materno</label>
                            <input type="text" name="apellido_materno" id="edit_apellido_materno" class="form-control"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contraseña</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Nueva contraseña (opcional)">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold border-top pt-2 d-block">Roles del sistema</label>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                @foreach ($roles as $rol)
                                    <div class="form-check">
                                        <input class="form-check-input edit-role-checkbox" type="checkbox"
                                            name="roles[]" value="{{ $rol->name }}"
                                            id="edit_role_{{ $loop->index }}">
                                        <label class="form-check-label" for="edit_role_{{ $loop->index }}">
                                            {{ $rol->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
