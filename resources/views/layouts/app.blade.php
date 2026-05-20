<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FishSpot Aragón') — FishSpot Aragón</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <header class="header">
        <nav class="nav container">
            <a href="{{ route('publicaciones.index') }}" class="nav__logo">
                FishSpot Aragón
            </a>

            <ul class="nav__links">
                <li><a href="{{ route('publicaciones.index') }}" class="nav__link {{ request()->routeIs('publicaciones.index') ? 'nav__link--active' : '' }}">Mapa</a></li>

                @auth
                    <li><a href="{{ route('publicaciones.create') }}" class="nav__link nav__link--highlight">+ Nueva zona</a></li>
                    <li><a href="{{ route('perfil.show') }}" class="nav__link {{ request()->routeIs('perfil.*') ? 'nav__link--active' : '' }}">Mi perfil</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline">
                            @csrf
                            <button type="submit" class="nav__link nav__link--btn">Salir</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="nav__link">Iniciar sesión</a></li>
                    <li><a href="{{ route('register') }}" class="nav__link nav__link--highlight">Registrarse</a></li>
                @endauth
            </ul>
        </nav>
    </header>

    <main class="main">
        <div class="container">
            @if(session('success'))
                <div class="alert alert--success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert--error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert--error">
                    <ul class="alert__list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
