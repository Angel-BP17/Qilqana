<div class="modal fade" id="editEntityModal" tabindex="-1" aria-labelledby="editEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editEntityModalLabel">Editar entidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editEntityForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" id="edit_entity_name" class="form-control"
                                placeholder="Nombre de la entidad" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Código</label>
                            <input type="text" name="code" id="edit_entity_code" class="form-control"
                                placeholder="Ej. 150123" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tipo</label>
                            <select name="school_type" id="edit_entity_type" class="form-select" required>
                                <option value="">Selecciona un tipo</option>
                                @foreach ($entityTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Distrito</label>
                            <input type="text" name="district" id="edit_entity_district" class="form-control"
                                placeholder="Distrito" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Contacto</label>
                            <input type="text" name="contact_number" id="edit_entity_contact" class="form-control"
                                placeholder="Opcional">
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


