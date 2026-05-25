<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <title>FishSpot — Bienvenido</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        html, body { height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            min-height: 100vh;
            background: url('{{ asset('images/pesca2.png') }}') center / cover no-repeat fixed;
            background-color: #c8dff0;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(245, 250, 255, 0.80);
            z-index: 0;
            pointer-events: none;
        }
        .splash-wrap {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4rem;
            min-height: 100vh;
            padding: 2rem 1.5rem;
        }
        .splash-brand {
            max-width: 400px;
            color: #1A1A1A;
            flex-shrink: 0;
        }
        .splash-brand__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #0099FF;
            background: rgba(0, 153, 255, 0.10);
            border: 1px solid rgba(0, 153, 255, 0.22);
            padding: 0.3rem 0.7rem;
            border-radius: 100px;
            margin-bottom: 1rem;
        }
        .splash-brand__name {
            font-size: 2.75rem;
            font-weight: 800;
            letter-spacing: -0.055em;
            line-height: 1.05;
            margin-bottom: 1rem;
            color: #1A1A1A;
        }
        .splash-brand__name span { color: #0099FF; }
        .splash-brand__tagline {
            font-size: 1.025rem;
            color: #5B6472;
            line-height: 1.65;
            margin-bottom: 1.875rem;
        }
        .splash-brand__features {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }
        .splash-feature {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            font-size: 0.88rem;
            color: #374151;
            font-weight: 500;
        }
        .splash-feature__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px; height: 20px;
            border-radius: 50%;
            background: rgba(0, 153, 255, 0.12);
            color: #0099FF;
            font-size: 0.7rem;
            font-weight: 800;
            flex-shrink: 0;
        }
        /* Auth card */
        .splash-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 24px;
            padding: 2.25rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 40px rgba(0, 153, 255, 0.12), 0 2px 12px rgba(0, 0, 0, 0.07);
            flex-shrink: 0;
        }
        .splash-card__heading {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.035em;
            margin-bottom: 1.5rem;
            color: #1A1A1A;
        }
        /* Tab switcher */
        .splash-tabs {
            display: flex;
            gap: 0.25rem;
            background: #F0F2F5;
            border-radius: 12px;
            padding: 0.25rem;
            margin-bottom: 1.75rem;
        }
        .splash-tab {
            flex: 1;
            padding: 0.525rem 0.5rem;
            border-radius: 9px;
            font-size: 0.84rem;
            font-weight: 600;
            color: #6B7280;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background 0.15s, color 0.15s, box-shadow 0.15s;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .splash-tab--active {
            background: #0099FF;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0, 153, 255, 0.35);
        }
        .splash-pane { display: none; }
        .splash-pane--active { display: block; }
        /* Responsive */
        @media (max-width: 800px) {
            .splash-wrap { flex-direction: column; gap: 1.75rem; padding: 2rem 1rem 3rem; align-items: stretch; }
            .splash-brand { text-align: center; max-width: 100%; }
            .splash-brand__name { font-size: 2.1rem; }
            .splash-brand__features { display: none; }
            .splash-card { max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="splash-wrap">

    {{-- Branding --}}
    <div class="splash-brand">
        <div class="splash-brand__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="10" height="10">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Comunidad de pescadores
        </div>
        <h1 class="splash-brand__name">FishSpot<br><span>España</span></h1>
        <p class="splash-brand__tagline">
            Descubre, comparte y explora las mejores<br>zonas de pesca de España con la comunidad.
        </p>
        <div class="splash-brand__features">
            <div class="splash-feature">
                <span class="splash-feature__icon">✓</span>
                Mapa interactivo de zonas de pesca
            </div>
            <div class="splash-feature">
                <span class="splash-feature__icon">✓</span>
                Fotos, descripciones y etiquetas
            </div>
            <div class="splash-feature">
                <span class="splash-feature__icon">✓</span>
                Likes, reposts y publicaciones guardadas
            </div>
            <div class="splash-feature">
                <span class="splash-feature__icon">✓</span>
                Sigue a otros pescadores de toda España
            </div>
        </div>
    </div>

    {{-- Auth Card --}}
    <div class="splash-card">
        @php $activePane = old('_form', 'login'); @endphp

        <p class="splash-card__heading">Accede a FishSpot</p>

        @if($errors->any())
            <div class="alert alert--error" style="margin-bottom:1.25rem">
                <ul class="alert__list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="splash-tabs">
            <button class="splash-tab {{ $activePane === 'login' ? 'splash-tab--active' : '' }}"
                    data-target="login">Iniciar sesión</button>
            <button class="splash-tab {{ $activePane === 'register' ? 'splash-tab--active' : '' }}"
                    data-target="register">Registrarse</button>
        </div>

        {{-- Login Pane --}}
        <div id="pane-login" class="splash-pane {{ $activePane === 'login' ? 'splash-pane--active' : '' }}">
            <form method="POST" action="{{ route('login') }}" class="form">
                @csrf
                <input type="hidden" name="_form" value="login">
                <div class="form__group">
                    <label class="form__label" for="sp-email">Email</label>
                    <input type="email" id="sp-email" name="email"
                           class="form-control @error('email') form-control--error @enderror"
                           value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>
                <div class="form__group">
                    <label class="form__label" for="sp-password">Contraseña</label>
                    <input type="password" id="sp-password" name="password"
                           class="form-control @error('password') form-control--error @enderror"
                           required autocomplete="current-password">
                </div>
                <div class="form__group form__group--inline">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Recordarme
                    </label>
                </div>
                <button type="submit" class="btn btn--primary btn--block">Entrar</button>
            </form>
        </div>

        {{-- Register Pane --}}
        <div id="pane-register" class="splash-pane {{ $activePane === 'register' ? 'splash-pane--active' : '' }}">
            <form method="POST" action="{{ route('register') }}" class="form">
                @csrf
                <input type="hidden" name="_form" value="register">
                <div class="form__group">
                    <label class="form__label" for="sp-name">Nombre</label>
                    <input type="text" id="sp-name" name="name"
                           class="form-control @error('name') form-control--error @enderror"
                           value="{{ old('name') }}" required autocomplete="name">
                </div>
                <div class="form__group">
                    <label class="form__label" for="sp-reg-email">Email</label>
                    <input type="email" id="sp-reg-email" name="email"
                           class="form-control @error('email') form-control--error @enderror"
                           value="{{ old('email') }}" required autocomplete="username">
                </div>
                <div class="form__group">
                    <label class="form__label" for="sp-reg-pass">Contraseña</label>
                    <input type="password" id="sp-reg-pass" name="password"
                           class="form-control @error('password') form-control--error @enderror"
                           required minlength="8" autocomplete="new-password">
                </div>
                <div class="form__group">
                    <label class="form__label" for="sp-pass-confirm">Confirmar contraseña</label>
                    <input type="password" id="sp-pass-confirm" name="password_confirmation"
                           class="form-control" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn--primary btn--block">Crear cuenta</button>
            </form>
        </div>
    </div>

</div>

<script>
(function () {
    var tabs  = document.querySelectorAll('.splash-tab');
    var panes = document.querySelectorAll('.splash-pane');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t)  { t.classList.remove('splash-tab--active'); });
            panes.forEach(function (p) { p.classList.remove('splash-pane--active'); });
            tab.classList.add('splash-tab--active');
            var pane = document.getElementById('pane-' + tab.dataset.target);
            if (pane) pane.classList.add('splash-pane--active');
        });
    });
}());
</script>
</body>
</html>
