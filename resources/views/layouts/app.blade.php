<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FishSpot') — FishSpot</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="@yield('body-class', 'app')">

@auth
{{-- ════════════════════════════════════════════
     3-COLUMN AUTHENTICATED LAYOUT
     ════════════════════════════════════════════ --}}

{{-- Mobile top bar --}}
<div class="mobile-bar">
    <a href="{{ route('publicaciones.index') }}" class="mobile-bar__logo">FishSpot</a>
    <a href="{{ route('perfil.show') }}" class="mobile-bar__avatar">
        @if(auth()->user()->avatar)
            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                 class="avatar avatar--sm" alt="{{ auth()->user()->name }}">
        @else
            <div class="avatar avatar--sm avatar--placeholder">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        @endif
    </a>
</div>

<div class="app-shell">

    {{-- ── Left Sidebar ── --}}
    <aside class="col-left">
        <div class="col-left__inner">

            <a href="{{ route('publicaciones.index') }}" class="sidebar-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                FishSpot
            </a>

            <nav class="sidebar-nav">
                <a href="{{ route('publicaciones.index') }}"
                   class="sidebar-nav__link {{ request()->routeIs('publicaciones.index') ? 'sidebar-nav__link--active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="18" height="18">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Inicio
                </a>

                <a href="{{ route('buscar') }}"
                   class="sidebar-nav__link {{ request()->routeIs('buscar') ? 'sidebar-nav__link--active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="18" height="18">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Explorar
                </a>

                <a href="{{ route('perfil.show') }}"
                   class="sidebar-nav__link {{ request()->routeIs('perfil.*') ? 'sidebar-nav__link--active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="18" height="18">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Mi perfil
                </a>

                <a href="{{ route('guardados.index') }}"
                   class="sidebar-nav__link {{ request()->routeIs('guardados.*') ? 'sidebar-nav__link--active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="18" height="18">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                    </svg>
                    Guardados
                </a>

                @php
                    $notifNoLeidas = \App\Models\Notificacion::where('user_id', auth()->id())->where('leida', false)->count();
                @endphp
                <a href="{{ route('notificaciones.index') }}"
                   class="sidebar-nav__link {{ request()->routeIs('notificaciones.*') ? 'sidebar-nav__link--active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="18" height="18">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    Notificaciones
                    @if($notifNoLeidas > 0)
                        <span class="sidebar-notif-badge">{{ $notifNoLeidas > 99 ? '99+' : $notifNoLeidas }}</span>
                    @endif
                </a>

                <a href="{{ route('guias.index') }}"
                   class="sidebar-nav__link {{ request()->routeIs('guias.*') ? 'sidebar-nav__link--active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="18" height="18">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        <line x1="10" y1="7" x2="16" y2="7"/>
                        <line x1="10" y1="11" x2="16" y2="11"/>
                        <line x1="10" y1="15" x2="14" y2="15"/>
                    </svg>
                    Equipos y Guías
                </a>

                <a href="{{ route('publicaciones.create') }}" class="sidebar-nav__link sidebar-nav__link--cta">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" width="18" height="18">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nueva zona
                </a>
            </nav>

            <div class="sidebar-profile">
                <a href="{{ route('perfil.show') }}" class="sidebar-profile__avatar-link">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                             class="avatar avatar--sm" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="avatar avatar--sm avatar--placeholder">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </a>
                <div class="sidebar-profile__info">
                    <div class="sidebar-profile__name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-profile__email">{{ auth()->user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
                    @csrf
                    <button type="submit" class="sidebar-logout" title="Cerrar sesión">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="16" height="16">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>

        </div>
    </aside>

    {{-- ── Center Column ── --}}
    <main class="col-center">
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
    </main>

    {{-- ── Right Sidebar ── --}}
    <aside class="col-right">
        <div class="col-right__inner">

            {{-- User Search --}}
            <form class="search-box" action="{{ route('usuarios.buscar') }}" method="GET">
                <div class="search-box__wrap">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.75" width="15" height="15">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <input type="search" name="q" class="search-input"
                           placeholder="Buscar usuario..."
                           value="{{ request()->routeIs('usuarios.buscar') ? request('q') : '' }}"
                           autocomplete="off">
                </div>
            </form>

            {{-- Recent zones widget ── moved from homepage ── --}}
            @php
                $sidebarRecientes = \App\Models\Publicacion::with(['imagenes','user','likes'])
                    ->latest()->take(6)->get();
            @endphp
            @if($sidebarRecientes->isNotEmpty())
                <div class="sidebar-recent">
                    <p class="sidebar-section-title">Zonas recientes</p>
                    @foreach($sidebarRecientes as $srPub)
                        @php $srImg = $srPub->imagenes->first(); @endphp
                        <a href="{{ route('publicaciones.show', $srPub) }}" class="sidebar-zone">
                            @if($srImg)
                                <img src="{{ asset('storage/' . $srImg->ruta) }}"
                                     alt="{{ $srPub->titulo }}" class="sidebar-zone__img">
                            @else
                                <div class="sidebar-zone__img--empty">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="sidebar-zone__info">
                                <p class="sidebar-zone__title">{{ $srPub->titulo }}</p>
                                <div class="sidebar-zone__meta">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                    {{ $srPub->likes->count() }}
                                    <span style="opacity:.4">·</span>
                                    {{ $srPub->user->name }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- CTA --}}
            <div class="sidebar-tip">
                <p class="sidebar-tip__title">FishSpot España</p>
                <p class="sidebar-tip__text">Comparte las mejores zonas de pesca de España con la comunidad.</p>
                <a href="{{ route('publicaciones.create') }}" class="btn btn--primary btn--block" style="margin-top:.75rem">
                    + Publicar zona
                </a>
            </div>

        </div>
    </aside>

</div>

{{-- Mobile bottom nav --}}
<nav class="mobile-nav">
    <a href="{{ route('publicaciones.index') }}"
       class="mobile-nav__item {{ request()->routeIs('publicaciones.index') ? 'mobile-nav__item--active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
    </a>
    <a href="{{ route('buscar') }}"
       class="mobile-nav__item {{ request()->routeIs('buscar') ? 'mobile-nav__item--active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
    </a>
    <a href="{{ route('publicaciones.create') }}" class="mobile-nav__item mobile-nav__item--cta">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
    </a>
    <a href="{{ route('guardados.index') }}"
       class="mobile-nav__item {{ request()->routeIs('guardados.*') ? 'mobile-nav__item--active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
        </svg>
    </a>
    <a href="{{ route('perfil.show') }}"
       class="mobile-nav__item {{ request()->routeIs('perfil.*') ? 'mobile-nav__item--active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
    </a>
</nav>

@else
{{-- ════════════════════════════════════════════
     GUEST LAYOUT (login / register pages)
     ════════════════════════════════════════════ --}}

<header class="header">
    <nav class="nav container">
        <a href="{{ route('publicaciones.index') }}" class="nav__logo">FishSpot</a>
        <ul class="nav__links">
            <li><a href="{{ route('login') }}" class="nav__link {{ request()->routeIs('login') ? 'nav__link--active' : '' }}">Iniciar sesión</a></li>
            <li><a href="{{ route('register') }}" class="nav__link nav__link--highlight">Registrarse</a></li>
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

@endauth

@stack('modals')
<script>
/* Global 3-dot dropdown handler */
(function () {
    document.addEventListener('click', function (e) {
        var inMenu = e.target.closest('.post-actions');
        var trigger = e.target.closest('[data-actions-trigger]');

        /* Close every open dropdown that is not the one we're interacting with */
        document.querySelectorAll('.post-actions__dropdown.is-open').forEach(function (d) {
            if (!trigger || d !== trigger.closest('.post-actions').querySelector('.post-actions__dropdown')) {
                d.classList.remove('is-open');
            }
        });

        if (trigger) {
            e.stopPropagation();
            var dropdown = trigger.closest('.post-actions').querySelector('.post-actions__dropdown');
            dropdown.classList.toggle('is-open');
        }
    });

    /* Close on Escape */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.post-actions__dropdown.is-open').forEach(function (d) {
                d.classList.remove('is-open');
            });
        }
    });
}());
</script>
@stack('scripts')
</body>
</html>
