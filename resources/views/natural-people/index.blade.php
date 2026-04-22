@extends('layouts.app')

@section('title', 'Personas naturales')
@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <span class="material-symbols-outlined me-2">person</span>Personas Naturales
                </h3>
                <p class="text-white-50 mb-0">Administración de ciudadanos y administrados registrados</p>
            </div>
        </div>

        @php
            $canCreateNaturalPerson = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('natural-people.create');
        @endphp
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center p-4 gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 flex-shrink-0">
                            <span class="material-symbols-outlined fs-4">person</span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small text-uppercase fw-bold">Total registrados</p>
                            <h3 class="mb-0 fw-bold">{{ $naturalPeople->total() ?? $naturalPeople->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <p class="mb-1 text-muted small text-uppercase fw-bold">Nueva persona natural</p>
                                <h6 class="mb-0 text-muted">Registro rápido de ciudadanos</h6>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                                <a class="btn btn-outline-primary" href="{{ route('natural-people.download-template') }}">
                                    <span class="material-symbols-outlined me-1">download</span> Plantilla
                                </a>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#createNaturalPersonModal" @disabled(!$canCreateNaturalPerson)>
                                    <span class="material-symbols-outlined me-1">add_circle</span> Registrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($canCreateNaturalPerson)
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="fw-bold text-uppercase small text-muted mb-3">
                                <span class="material-symbols-outlined me-1">upload</span>Importación masiva
                            </div>
                            <form method="POST" action="{{ route('natural-people.import') }}"
                                enctype="multipart/form-data" id="naturalPeopleImportForm">
                                @csrf
                                <input type="hidden" name="update_existing" id="natural_people_update_existing" value="1">
                                <div class="row g-3">
                                    <div class="col-12 col-md-7 col-xl-8">
                                        <input type="file" class="form-control" id="archivo_excel_natural_people"
                                            data-import-input="natural-people" name="archivo_excel" accept=".xlsx,.xls">
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('natural-people.download-template') }}"
                                                class="btn btn-info text-white flex-grow-1">
                                                <span class="material-symbols-outlined me-1">download</span>Plantilla
                                            </a>
                                            <button type="button"
                                                class="btn btn-success flex-grow-1"
                                                id="importNaturalPeopleButton" data-bs-toggle="modal"
                                                data-bs-target="#importNaturalPeopleModal" disabled>
                                                <span class="material-symbols-outlined me-1">upload</span> Importar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="text-muted small mt-3">
                                <span class="material-symbols-outlined me-1">info</span> Use la plantilla oficial para asegurar la integridad de los datos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @include('natural-people.partials.table')
    </div>

    @include('natural-people.forms.create')
    @include('natural-people.forms.edit')

    <div class="modal fade" id="deleteNaturalPersonModal" tabindex="-1" aria-labelledby="deleteNaturalPersonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteNaturalPersonModalLabel">Eliminar persona natural</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="deleteNaturalPersonForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">Indica el motivo de la eliminacion.</p>
                        <div class="mb-0">
                            <label for="delete_natural_person_reason" class="form-label">Razon</label>
                            <textarea class="form-control" id="delete_natural_person_reason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importNaturalPeopleModal" tabindex="-1" aria-labelledby="importNaturalPeopleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importNaturalPeopleModalLabel">Importar personas naturales</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Elige qué hacer con registros que ya existen en la base de datos.</p>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="update_existing_choice"
                            id="import_update_existing" value="1" checked>
                        <label class="form-check-label" for="import_update_existing">
                            Actualizar registros existentes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="update_existing_choice"
                            id="import_skip_existing" value="0">
                        <label class="form-check-label" for="import_skip_existing">
                            No tocarlos (pasar a la siguiente fila)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmNaturalPeopleImport">
                        Importar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
