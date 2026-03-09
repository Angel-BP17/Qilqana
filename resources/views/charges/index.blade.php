@extends('layouts.app')

@section('title', 'Cargos')
@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('errores'))
            <div class="alert alert-warning mt-3 shadow-sm">
                <h5 class="mb-2">Errores durante la importacion:</h5>
                <ul class="mb-0">
                    @foreach (session('errores') as $error)
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
        <div id="charges-dashboard" data-refresh-interval="{{ $refreshIntervalSeconds ?? 5 }}" data-refresh-url="{{ route('charges.refresh') }}">
            @include('charges.partials.dashboard')
        </div>
    </div>
    {{-- Formularios de crear cargos --}}
    @include('charges.forms.create')

    {{-- Formularios de editar cargos --}}
    @include('charges.forms.edit')

    {{-- Formularios de firmar cargos --}}
    @include('charges.forms.sign')

    {{-- Formularios de rechazar cargos --}}
    @include('charges.forms.reject')

    {{-- Formularios de ver firma de cargo --}}
    @include('charges.forms.view-signature')

    {{-- Formularios de eliminar cargos --}}
    <div class="modal fade" id="deleteChargeModal" tabindex="-1" aria-labelledby="deleteChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteChargeModalLabel">Eliminar cargo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="deleteChargeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">Indica el motivo de la eliminación.</p>
                        <div class="mb-0">
                            <label for="delete_reason" class="form-label">Razón</label>
                            <textarea class="form-control" id="delete_reason" name="reason" rows="3" required></textarea>
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


    {{-- Modal ver carta poder --}}
    <div class="modal fade" id="viewCartaPoderModal" tabindex="-1" aria-labelledby="viewCartaPoderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewCartaPoderModalLabel">Carta poder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="viewCartaPoderContent" class="border rounded p-3 bg-white text-center"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/charges.js'])
@endsection

