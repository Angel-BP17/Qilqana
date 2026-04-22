@extends('layouts.app')

@section('title', 'Entidades')
@section('content')
    <div class="container">
        @php
            $canCreateEntity = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('entities.create');
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
                            <span class="material-symbols-outlined fs-4">school</span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total de entidades</p>
                            <h4 class="mb-0 fw-bold">{{ $entities->total() ?? $entities->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Nueva entidad</p>
                            <h6 class="mb-0">Registra una entidad rápida</h6>
                        </div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#createEntityModal" @disabled(!$canCreateEntity)
                            @unless ($canCreateEntity) title="No tienes permiso para crear entidades" @endunless>
                            <span class="material-symbols-outlined">add_circle</span> Registrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('entities.partials.table')
    </div>

    @include('entities.forms.create')
    @include('entities.forms.edit')

    <div class="modal fade" id="deleteEntityModal" tabindex="-1" aria-labelledby="deleteEntityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteEntityModalLabel">Eliminar entidad</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="deleteEntityForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">Indica el motivo de la eliminacion.</p>
                        <div class="mb-0">
                        <label for="delete_entity_reason" class="form-label">Razon</label>
                        <textarea class="form-control" id="delete_entity_reason" name="reason" rows="3" required></textarea>
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

