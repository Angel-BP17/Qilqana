<form class="d-flex flex-wrap gap-2 flex-grow-1 flex-xl-grow-0 justify-content-xl-end" action="{{ route('charges.index') }}" method="GET">
    <div class="flex-grow-1" style="min-width: 200px; max-width: 350px;">
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <span class="material-symbols-outlined text-muted">search</span>
            </span>
            <input type="text" class="form-control border-start-0" name="search"
                placeholder="Buscar por cargo, RUC, DNI..." value="{{ request('search') }}">
        </div>
    </div>
    <div style="width: 130px;">
        <select name="signature_status" class="form-select" onchange="this.form.submit()">
            <option value="">Estado</option>
            <option value="pendiente" @selected(request('signature_status') === 'pendiente')>Pendientes</option>
            <option value="firmado" @selected(request('signature_status') === 'firmado')>Firmados</option>
            <option value="rechazado" @selected(request('signature_status') === 'rechazado')>Rechazados</option>
        </select>
    </div>
    <div style="width: 110px;">
        <select name="period" class="form-select" onchange="this.form.submit()">
            <option value="">Periodo</option>
            @foreach ($periodOptions ?? [] as $period)
                <option value="{{ $period }}" @selected(($sentPeriod ?? request('period')) === $period)>
                    {{ $period }}
                </option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-light" type="submit" title="Filtrar">
        <span class="material-symbols-outlined">filter_alt</span>
        <span class="d-md-none d-xxl-inline">Filtrar</span>
    </button>
</form>
