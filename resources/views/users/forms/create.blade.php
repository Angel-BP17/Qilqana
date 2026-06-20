<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createUserModalLabel">Registrar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">DNI</label>
                            <div class="input-group">
                                <input type="text" name="dni" id="create_user_dni" class="form-control" required maxlength="10" placeholder="Ingrese DNI">
                                <button class="btn btn-outline-primary" type="button" id="lookup_user_dni_btn">Buscar</button>
                            </div>
                            <div id="create_user_dni_error" class="text-danger small mt-1 d-none"></div>
                        </div>
                        <div class="col-md-6 d-none user-details-fields">
                            <label class="form-label fw-bold">Nombres</label>
                            <input type="text" name="name" id="create_user_name" class="form-control" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();" required placeholder="Nombres">
                        </div>
                        <div class="col-md-6 d-none user-details-fields">
                            <label class="form-label fw-bold">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" id="create_user_apellido_paterno" class="form-control"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();"
                                required placeholder="Apellido Paterno">
                        </div>
                        <div class="col-md-6 d-none user-details-fields">
                            <label class="form-label fw-bold">Apellido Materno</label>
                            <input type="text" name="apellido_materno" id="create_user_apellido_materno" class="form-control"
                                style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();"
                                required placeholder="Apellido Materno">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contraseña</label>
                            <input type="password" name="password" class="form-control" required placeholder="Contraseña">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold border-top pt-2 d-block">Roles del sistema</label>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                @foreach ($roles as $rol)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                            value="{{ $rol->name }}" id="create_role_{{ $loop->index }}">
                                        <label class="form-check-label" for="create_role_{{ $loop->index }}">
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
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
