<form class="d-flex flex-wrap gap-2" action="{{ route('charges.index') }}" method="GET">
    <div class="col">
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <span class="material-symbols-outlined text-muted">search</span>
            </span>
            <input type="text" class="form-control border-start-0" name="search"
                placeholder="No de cargo, RUC, DNI, nombres o asunto" value="{{ request('search') }}">
        </div>
    </div>
    <div class="col-2">
        <select name="signature_status" class="form-select" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="pendiente" @selected(request('signature_status') === 'pendiente')>Pendientes</option>
            <option value="firmado" @selected(request('signature_status') === 'firmado')>Firmados</option>
            <option value="rechazado" @selected(request('signature_status') === 'rechazado')>Rechazados</option>
        </select>
    </div>
    <div class="col-2">
        <select name="period" class="form-select" onchange="this.form.submit()">
            <option value="">Todos los periodos</option>
            @foreach ($periodOptions ?? [] as $period)
                <option value="{{ $period }}" @selected(($sentPeriod ?? request('period')) === $period)>
                    {{ $period }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-light" type="submit"><span class="material-symbols-outlined">filter_alt</span> Filtrar</button>
    </div>
</form>
