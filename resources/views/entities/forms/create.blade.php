<div class="modal fade" id="createEntityModal" tabindex="-1" aria-labelledby="createEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createEntityModalLabel">Registrar entidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('entities.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" class="form-control" placeholder="Nombre de la entidad" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Código</label>
                            <input type="text" name="code" class="form-control" placeholder="Ej. 150123" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tipo</label>
                            <select name="school_type" class="form-select" required>
                                <option value="">Selecciona un tipo</option>
                                @foreach ($entityTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Distrito</label>
                            <input type="text" name="district" class="form-control" placeholder="Distrito" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Contacto</label>
                            <input type="text" name="contact_number" class="form-control"
                                placeholder="Opcional">
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



