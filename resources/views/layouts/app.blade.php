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
                                    <li>
                                        <a class="dropdown-item" href="{{ route('level-modalities.index') }}">Modalidades / Niveles</a>
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
                        <!-- Notificaciones de Cargos Pendientes de Firma -->
                        <li class="nav-item dropdown me-2" id="navNotificationsWrapper">
                            <a class="nav-link px-lg-2 position-relative" href="#" id="navNotificationsDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Cargos pendientes de firmar">
                                <span class="material-symbols-outlined align-middle fs-4">notifications</span>
                                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size: 0.65rem;">
                                    0
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border border-light-subtle rounded-3 py-2" 
                                aria-labelledby="navNotificationsDropdown" id="notificationsList" 
                                style="width: 320px; max-height: 420px; overflow-y: auto;">
                                <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">Notificaciones</span>
                                    <span id="notificationPendingText" class="fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">0 PENDIENTES</span>
                                </li>
                                <div id="notificationItems" class="py-1">
                                    <li class="text-center py-3 text-muted small">
                                        <span class="material-symbols-outlined d-block mb-1 fs-5">notifications_off</span>
                                        No tienes cargos pendientes
                                    </li>
                                </div>
                                <li class="border-top text-center pt-2 mt-1">
                                    <a class="dropdown-item py-1 small text-muted text-center fw-medium" href="#" 
                                        onclick="event.preventDefault(); bootstrap.Dropdown.getInstance(document.getElementById('navNotificationsDropdown')).hide();">
                                        Cerrar panel
                                    </a>
                                </li>
                            </ul>
                        </li>

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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkNotifications = async () => {
                try {
                    const response = await fetch("{{ route('notifications.pending-charges') }}", {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    
                    const badge = document.getElementById('notificationBadge');
                    const pendingText = document.getElementById('notificationPendingText');
                    const itemsContainer = document.getElementById('notificationItems');
                    
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('d-none');
                        pendingText.textContent = `${data.count} PENDIENTES`;
                        pendingText.classList.remove('text-muted');
                        pendingText.classList.add('text-primary');
                        
                        let html = '';
                        data.charges.forEach(charge => {
                            html += `
                                <li class="px-3 py-2 border-bottom position-relative notification-item-unread" style="background-color: rgba(13, 110, 253, 0.04);">
                                    <div class="d-flex align-items-start gap-2">
                                        <!-- Punto azul de no leído -->
                                        <span class="text-primary mt-1" style="font-size: 0.95rem; line-height: 1; user-select: none;">●</span>
                                        <div class="flex-grow-1">
                                            <div class="text-dark small lh-sm mb-1" style="font-size: 0.82rem; font-weight: 500;">
                                                Se le ha asignado un nuevo cargo para firmar: <strong>${charge.label}</strong>
                                                <span class="text-muted d-block text-truncate mt-0.5" style="max-width: 250px; font-size: 0.75rem; font-weight: 400;">
                                                    ${charge.asunto}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <span class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                                    <span class="material-symbols-outlined fs-6 align-middle" style="font-size: 0.85rem;">schedule</span>
                                                    Hace ${charge.created_at}
                                                </span>
                                                <button type="button" class="btn btn-link text-decoration-none p-0 d-inline-flex align-items-center gap-0.5 btn-mark-read" 
                                                    data-id="${charge.id}" style="font-size: 0.72rem; font-weight: 600; color: #0d6efd;">
                                                    <span class="material-symbols-outlined" style="font-size: 0.95rem; font-weight: bold;">check</span>
                                                    Marcar como leída
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            `;
                        });
                        itemsContainer.innerHTML = html;
                        
                        // Añadir los event listeners para marcar como leída
                        itemsContainer.querySelectorAll('.btn-mark-read').forEach(btn => {
                            btn.addEventListener('click', async function(e) {
                                e.stopPropagation(); // Evitar que se cierre el dropdown
                                e.preventDefault();
                                const notifId = this.dataset.id;
                                const url = "{{ route('notifications.read', ':id') }}".replace(':id', notifId);
                                try {
                                    const resRead = await fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    });
                                    if (resRead.ok) {
                                        checkNotifications(); // Recargar notificaciones
                                    }
                                } catch (err) {
                                    console.error('Error marking notification as read:', err);
                                }
                            });
                        });
                    } else {
                        badge.classList.add('d-none');
                        badge.textContent = '0';
                        pendingText.textContent = '0 PENDIENTES';
                        pendingText.classList.remove('text-primary');
                        pendingText.classList.add('text-muted');
                        itemsContainer.innerHTML = `
                            <li class="text-center py-4 text-muted small">
                                <span class="material-symbols-outlined d-block mb-1 fs-5 text-secondary">notifications_off</span>
                                No tienes cargos pendientes
                            </li>
                        `;
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            };

            // Ejecutar inmediatamente al cargar y luego cada 10 segundos
            checkNotifications();
            setInterval(checkNotifications, 10000);
        });
    </script>
</body>

</html>
