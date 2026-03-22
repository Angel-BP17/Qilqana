<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Inicio')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-primary">
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
                        @if (Auth::user()->hasRole('ADMINISTRADOR') ||
                                Auth::user()->can('modulo cargos') ||
                                Auth::user()->can('modulo resoluciones'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-lg-2" href="#" id="navChargesDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Cargos y resoluciones
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navChargesDropdown">
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo cargos'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('charges.index') }}">Cargos
                                                varios</a>
                                        </li>
                                    @endif
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo resoluciones'))
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('resolucions.index') }}">Resoluciones</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (Auth::user()->hasRole('ADMINISTRADOR') ||
                                Auth::user()->can('modulo personas naturales') ||
                                Auth::user()->can('modulo personas juridicas'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-lg-2" href="#" id="navInterestedDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Interesados
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navInterestedDropdown">
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo personas naturales'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('natural-people.index') }}">Personas
                                                naturales</a>
                                        </li>
                                    @endif
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo personas juridicas'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('legal-entities.index') }}">Personas
                                                juridicas</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo usuarios') || Auth::user()->can('modulo roles'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-lg-2" href="#" id="navUsersDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Usuarios y roles
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navUsersDropdown">
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo usuarios'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.index') }}">Usuarios</a>
                                        </li>
                                    @endif
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo roles'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('roles.index') }}">Roles</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (Auth::user()->hasRole('ADMINISTRADOR') ||
                                Auth::user()->can('modulo configuracion') ||
                                Auth::user()->can('modulo registro de actividades'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-lg-2" href="#" id="navMoreDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Mas
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navMoreDropdown">
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo configuracion'))
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('settings.index') }}">Configuracion</a>
                                        </li>
                                    @endif
                                    @if (Auth::user()->hasRole('ADMINISTRADOR') || Auth::user()->can('modulo registro de actividades'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('activity-logs.index') }}">Registro
                                                de actividades</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        <li class="nav-item">
                            <span class="nav-link text-muted px-lg-2">
                                <i class="fa-solid fa-user me-1"></i>{{ Auth::user()->name }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="nav-link logout px-lg-2">
                                <span class="d-none d-sm-inline">Logout</span> <i class="fa fa-sign-out"></i>
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
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('scripts')
</body>

</html>
