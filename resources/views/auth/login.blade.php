<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">
    <title>Login</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="custom-auth-container" data-page="login">
    <div class="custom-login-container">
        <div class="custom-auth-card">
            <div class="card-body p-4 p-sm-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('img/mesa de trabajo.png') }}" alt="Logo"
                        class="rounded mx-auto mb-3 w-75 h-75">
                    <h4>Iniciar Sesión</h4>
                    <p class="text-muted">Ingresa a tu cuenta</p>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger mt-3 p-0 pt-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="dni" name="dni" placeholder="DNI"
                            required>
                        <label for="dni">DNI</label>
                        @error('dni')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Contraseña">
                        <label for="password">Contraseña</label>
                        <span class="material-symbols-outlined position-absolute top-50 end-0 translate-middle-y me-3"
                                                    id="togglePassword" style="cursor: pointer; user-select: none;">visibility_off</span>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Mantener sesión iniciada
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg" type="submit">Iniciar Sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
