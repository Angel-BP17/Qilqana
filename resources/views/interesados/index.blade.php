@extends('layouts.app')

@section('title', 'Interesados')
@section('content')
    <div class="container">
        @php
            $canCreateInteresado = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('interesados.create');
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
                            <span class="material-symbols-outlined fs-4">person</span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total de interesados</p>
                            <h4 class="mb-0 fw-bold">{{ $interesados->total() ?? $interesados->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Nuevo interesado</p>
                            <h6 class="mb-0">Registra un interesado rápido</h6>
                        </div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#createInteresadoModal" @disabled(!$canCreateInteresado)
                            @unless ($canCreateInteresado) title="No tienes permiso para crear interesados" @endunless>
                            <span class="material-symbols-outlined">add_circle</span> Registrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('interesados.partials.table')
    </div>

    @include('interesados.forms.create')
    @include('interesados.forms.edit')

    <div class="modal fade" id="deleteInteresadoModal" tabindex="-1" aria-labelledby="deleteInteresadoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteInteresadoModalLabel">Eliminar interesado</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="deleteInteresadoForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">Indica el motivo de la eliminación.</p>
                        <div class="mb-0">
                            <label for="delete_interesado_reason" class="form-label">Razón</label>
                            <textarea class="form-control" id="delete_interesado_reason" name="reason" rows="3" required></textarea>
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

