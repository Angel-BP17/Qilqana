@extends('layouts.app')

@section('title', 'Configuración')
@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <i class="fa-solid fa-gears me-2"></i>Configuración del Sistema
                </h3>
                <p class="text-white-50 mb-0">Ajustes generales, gestión de periodos y mantenimiento de datos</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Parámetros generales</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="charges_refresh_interval" class="form-label">Actualizar cargos cada
                                    (segundos)</label>
                                <input type="number" class="form-control" id="charges_refresh_interval"
                                    name="charges_refresh_interval" min="3" max="3600"
                                    value="{{ old('charges_refresh_interval', $refreshInterval) }}" required>
                                <div class="form-text">Se aplica al refresco automático del panel de cargos.</div>
                            </div>
                            <div class="mb-3">
                                <label for="charge_period" class="form-label">Periodo de numeración de cargos</label>
                                <input type="text" class="form-control" id="charge_period" name="charge_period"
                                    value="{{ old('charge_period', $chargePeriod) }}" maxlength="4" inputmode="numeric"
                                    pattern="\d{4}" placeholder="YYYY">
                                <div class="form-text">La numeración de cargos se reinicia por periodo.</div>
                            </div>
                            <button type="submit" class="btn btn-success">Guardar configuración</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Backups y restauración</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <form method="POST" action="{{ route('settings.backup') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-light bg-dark">
                                    <i class="fa-solid fa-download me-1"></i> Descargar backup
                                </button>
                            </form>
                        </div>
                        <div class="mb-3">
                            <form method="POST" action="{{ route('settings.import') }}" enctype="multipart/form-data">
                                @csrf
                                <label for="backup_file" class="form-label">Importar backup</label>
                                <input type="file" class="form-control mb-2" id="backup_file" name="backup_file"
                                    accept=".zip" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-upload me-1"></i> Importar
                                </button>
                            </form>
                        </div>
                        <div class="alert alert-warning mb-3">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Importar un backup reemplazará los datos actuales.
                        </div>
                        <form method="POST" action="{{ route('settings.reset') }}"
                            onsubmit="return confirm('¿Seguro que deseas reiniciar el sistema? Se eliminarán todos los datos y se ejecutarán los seeders.');">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-rotate me-1"></i> Reiniciar sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
