@extends('layouts.app')

@section('title', 'Usuarios')
@section('content')
    <div class="container">
        @php
            $canCreateUser = Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('users.create');
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
                            <i class="fa-solid fa-users fs-4"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total de usuarios</p>
                            <h4 class="mb-0 fw-bold">{{ $users->total() ?? $users->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Nuevo usuario</p>
                            <h6 class="mb-0">Registra un usuario rapidamente</h6>
                        </div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#createUserModal" @disabled(!$canCreateUser)
                            @unless ($canCreateUser) title="No tienes permiso para crear usuarios" @endunless>
                            <i class="bi bi-person"></i> Registrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de usuarios --}}
        @include('users.partials.table')
    </div>

    {{-- Información detallada del usuario --}}
    @include('users.forms.show')

    {{-- Formularios de usuarios --}}
    @include('users.forms.create')
    @include('users.forms.edit')

    {{-- Modal eliminar usuario --}}
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Eliminar usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">Indica el motivo de la eliminación.</p>
                        <div class="mb-0">
                            <label for="delete_user_reason" class="form-label">Razón</label>
                            <textarea class="form-control" id="delete_user_reason" name="reason" rows="3" required></textarea>
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
    @vite(['resources/js/users.js'])
@endsection
