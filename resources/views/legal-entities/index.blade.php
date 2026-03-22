@extends('layouts.app')

@section('title', 'Personas juridicas')
@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-white mb-0">
                    <i class="fa-solid fa-building-user me-2"></i>Personas Jurídicas
                </h3>
                <p class="text-white-50 mb-0">Gestión de empresas, instituciones y sus representantes</p>
            </div>
        </div>

        @php
            $canCreateLegalEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('legal-entities.create');
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
                            <i class="fa-solid fa-building fs-4"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small text-uppercase fw-bold">Total entidades</p>
                            <h3 class="mb-0 fw-bold">{{ $legalEntities->total() ?? $legalEntities->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <p class="mb-1 text-muted small text-uppercase fw-bold">Nueva persona jurídica</p>
                                <h6 class="mb-0 text-muted">Registro de empresas e instituciones</h6>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                                <a class="btn btn-outline-primary" href="{{ route('legal-entities.download-template') }}">
                                    <i class="bi bi-download me-1"></i> Plantilla
                                </a>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#createLegalEntityModal" @disabled(!$canCreateLegalEntity)>
                                    <i class="bi bi-plus-circle me-1"></i> Registrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($canCreateLegalEntity)
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="fw-bold text-uppercase small text-muted mb-3">
                                <i class="fa-solid fa-file-import me-1"></i>Importación masiva
                            </div>
                            <form method="POST" action="{{ route('legal-entities.import') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12 col-md-7 col-xl-8">
                                        <input type="file" class="form-control" id="archivo_excel_legal_entities"
                                            data-import-input="legal-entities" name="archivo_excel" accept=".xlsx,.xls">
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('legal-entities.download-template') }}"
                                                class="btn btn-info text-white flex-grow-1">
                                                <i class="fas fa-file-download me-1"></i>Plantilla
                                            </a>
                                            <button type="submit"
                                                class="btn btn-success flex-grow-1"
                                                id="importLegalEntitiesButton" disabled>
                                                <i class="fa-solid fa-upload me-1"></i> Importar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="text-muted small mt-3">
                                <i class="fa-solid fa-circle-info me-1"></i> Use la plantilla oficial para asegurar la integridad de los datos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @include('legal-entities.partials.table')
    </div>

    @include('legal-entities.forms.create')
    @include('legal-entities.forms.edit')

    <div class="modal fade" id="deleteLegalEntityModal" tabindex="-1" aria-labelledby="deleteLegalEntityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteLegalEntityModalLabel">Eliminar persona juridica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="deleteLegalEntityForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">Indica el motivo de la eliminacion.</p>
                        <div class="mb-0">
                        <label for="delete_legal_entity_reason" class="form-label">Razon</label>
                        <textarea class="form-control" id="delete_legal_entity_reason" name="reason" rows="3" required></textarea>
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

@endsection

@section('scripts')
    @vite(['resources/js/legal-entities.js'])
@endsection
