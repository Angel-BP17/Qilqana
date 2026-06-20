<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">

    <title>@yield('title', 'Inicio')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-primary" data-page="{{ Route::currentRouteName() }}">
    <div id="app">
        <nav class="navbar navbar-expand-md shadow-sm bg-white">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('img/logo.png') }}" width="60" height="48" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
                        @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo resoluciones'))
                            {{-- Dropdown de Operaciones para Admin y Registrador de Resoluciones --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-lg-2" href="#" id="navOperationsDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Operaciones
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navOperationsDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('charges.index') }}">Cargos varios</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('resolucions.index') }}">Resoluciones</a>
                                    </li>
                                </ul>
                            </li>
                        @elseif (Auth::user()->can('modulo cargos'))
                            {{-- Enlace directo para Registradores simples --}}
                            <li class="nav-item">
                                <a class="nav-link px-lg-2" href="{{ route('charges.index') }}">Cargos</a>
                            </li>
                        @endif

                        @if (Auth::user()->hasRole('ADMINISTRADOR'))
                            {{-- Dropdown de Catálogos (Personas y tipos de res/asunto) --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-lg-2" href="#" id="navCatalogsDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Catálogos
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navCatalogsDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('natural-people.index') }}">Personas naturales</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('legal-entities.index') }}">Personas jurídicas</a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('resolucion-types.index') }}">Tipos de resolución</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('asunto-types.index') }}">Tipos de asunto</a>
                                    </li>
                                </ul>
                            </li>

                            {{-- Dropdown de Sistema (Usuarios, Roles, Configuración y Auditoría) --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-lg-2" href="#" id="navSystemDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Sistema
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navSystemDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index') }}">Usuarios</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('roles.index') }}">Roles</a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('settings.index') }}">Configuración</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('activity-logs.index') }}">Registro de actividades</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        <li class="nav-item">
                            <span class="nav-link text-muted px-lg-2">
                                <span class="material-symbols-outlined me-1">person</span>{{ Auth::user()->name }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="nav-link logout px-lg-2">
                                <span class="d-none d-sm-inline">Salir</span> <span class="material-symbols-outlined">logout</span>
                            </a>
                            <form class="d-flex" id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <x-flash-messages />
            </div>
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @yield('scripts')
</body>

</html>
