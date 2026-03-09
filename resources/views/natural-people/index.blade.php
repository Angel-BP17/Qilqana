@extends('layouts.app')

@section('title', 'Personas naturales')
@section('content')
    <div class="container">
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
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <i class="fa-solid fa-user fs-4"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total de personas naturales</p>
                            <h4 class="mb-0 fw-bold">{{ $naturalPeople->total() ?? $naturalPeople->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <p class="mb-1 text-muted">Nueva persona natural</p>
                            <h6 class="mb-0">Registra una persona natural rapida</h6>
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                            <a class="btn btn-outline-primary w-100 w-md-auto"
                                href="{{ asset('storage/templates/Plantilla_persona_natural.xlsx') }}" download>
                                <i class="bi bi-download"></i>
                                <span class="ms-1">Descargar plantilla</span>
                            </a>
                            <button type="button" class="btn btn-success w-100 w-md-auto" data-bs-toggle="modal"
                                data-bs-target="#createNaturalPersonModal" @disabled(!$canCreateNaturalPerson)
                                @unless ($canCreateNaturalPerson) title="No tienes permiso para crear personas naturales" @endunless>
                                <i class="bi bi-plus-circle"></i>
                                <span class="ms-1">Registrar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($canCreateNaturalPerson)
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="fw-semibold mb-2">
                                <i class="fa-solid fa-file-import me-1"></i>Importar personas naturales
                            </div>
                            <form method="POST" action="{{ route('natural-people.import') }}"
                                enctype="multipart/form-data" id="naturalPeopleImportForm">
                                @csrf
                                <input type="hidden" name="update_existing" id="natural_people_update_existing" value="1">
                                <div class="row g-2 align-items-stretch">
                                    <div class="col-12 col-lg-7">
                                        <input type="file" class="form-control h-100" id="archivo_excel_natural_people"
                                            data-import-input="natural-people" name="archivo_excel" accept=".xlsx,.xls">
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <div class="d-flex flex-wrap gap-2 h-100 align-items-stretch">
                                            <a href="{{ asset('storage/templates/Plantilla_persona_natural.xlsx') }}"
                                                class="btn btn-info h-100 d-flex align-items-center" download>
                                                <i class="fas fa-file-download me-1"></i>Plantilla
                                            </a>
                                            <button type="button"
                                                class="btn btn-success h-100 d-flex align-items-center"
                                                id="importNaturalPeopleButton" data-bs-toggle="modal"
                                                data-bs-target="#importNaturalPeopleModal" disabled>
                                                <i class="fa-solid fa-file-excel me-1"></i> Importar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="text-muted small mt-2">
                                Usa la plantilla indicada para cargar los datos.
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

@section('scripts')
    @vite(['resources/js/natural-people.js'])
@endsection
