@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="profile-wrapper">
    <div class="profile-hero">
        <div class="profile-banner">
            @if($user->banner)
                <img src="{{ asset('storage/' . $user->banner) }}" alt="" class="profile-banner__img">
            @else
                <div class="profile-banner__placeholder"></div>
            @endif
            @if($isOwnProfile)
                <a href="{{ route('perfil.edit') }}" class="profile-banner__edit-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Editar portada
                </a>
            @endif
        </div>
        <div class="profile-info-card">
            <div class="profile-info-card__avatar-wrap">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}"
                         alt="{{ $user->name }}" class="avatar avatar--xl">
                @else
                    <div class="avatar avatar--xl avatar--placeholder">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="profile-info-card__action">
                @if($isOwnProfile)
                    <a href="{{ route('perfil.edit') }}" class="btn btn--secondary btn--sm">Editar perfil</a>
                @elseif(Auth::check())
                    <form method="POST" action="{{ route('follows.toggle', $user) }}">
                        @csrf
                        <button type="submit"
                                class="btn {{ $esSeguido ? 'btn--siguiendo' : 'btn--primary' }} btn--sm">
                            {{ $esSeguido ? 'Siguiendo' : 'Seguir' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn--primary btn--sm">Seguir</a>
                @endif
            </div>
            <div class="profile-info-card__text">
                <h1 class="profile-top__name">{{ $user->name }}</h1>
                @if($user->bio)
                    <p class="profile-top__bio">{{ $user->bio }}</p>
                @endif
                <p class="profile-top__since">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="13" height="13">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Miembro desde {{ $user->created_at->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="profile-stats">
                <div class="stat-card">
                    <span class="stat-card__value">{{ $seguidos }}</span>
                    <span class="stat-card__label">Seguidos</span>
                </div>
                <div class="stat-card">
                    <span class="stat-card__value">{{ $seguidores }}</span>
                    <span class="stat-card__label">Seguidores</span>
                </div>
                <div class="stat-card">
                    <span class="stat-card__value">{{ $user->publicaciones->count() }}</span>
                    <span class="stat-card__label">Zonas</span>
                </div>
            </div>
        </div>
    </div>
    <div class="profile-nav" role="tablist">
        <button class="profile-tab profile-tab--active" data-tab="posts" role="tab">Publicaciones</button>
        <button class="profile-tab" data-tab="replies" role="tab">Respuestas</button>
        <button class="profile-tab" data-tab="likes" role="tab">Me gusta</button>
    </div>
    <div id="tab-posts" class="profile-pane profile-pane--active" role="tabpanel">
        @forelse($feed as $entry)
            @php $pub = $entry['item']; $primeraImg = $pub->imagenes->first(); @endphp

            <div class="profile-post-card">

                @if($entry['tipo'] === 'reposte')
                    <div class="profile-post-card__repost-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="13" height="13">
                            <polyline points="17 1 21 5 17 9"/>
                            <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                            <polyline points="7 23 3 19 7 15"/>
                            <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                        </svg>
                        Reposteado · <span style="font-weight:500;opacity:.7">{{ $entry['fecha']->diffForHumans() }}</span>
                    </div>
                @endif

                @if($primeraImg)
                    <a href="{{ route('publicaciones.show', $pub) }}">
                        <img src="{{ asset('storage/' . $primeraImg->ruta) }}"
                             alt="{{ $pub->titulo }}" class="profile-post-card__img">
                    </a>
                @else
                    <div class="profile-post-card__img--placeholder">Sin imagen</div>
                @endif

                <div class="profile-post-card__body">
                    <h3 class="profile-post-card__title">
                        <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                    </h3>

                    @if($pub->descripcion)
                        <p class="profile-post-card__desc">{{ $pub->descripcion }}</p>
                    @endif

                    @if($pub->etiquetas->isNotEmpty())
                        <div class="tags">
                            @foreach($pub->etiquetas->take(4) as $et)
                                <span class="tag">{{ $et->nombre }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="profile-post-card__footer">
                        <span class="profile-post-card__stat" style="color:var(--c-like)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            {{ $pub->likes->count() }}
                        </span>
                        <span class="profile-post-card__stat" style="color:var(--c-repost)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="17 1 21 5 17 9"/>
                                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                                <polyline points="7 23 3 19 7 15"/>
                                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                            </svg>
                            {{ $pub->repostes->count() }}
                        </span>
                        @if($entry['tipo'] !== 'reposte')
                            <span class="profile-post-card__stat" style="font-size:.74rem;color:#9CA3AF;margin-left:.25rem">
                                {{ $entry['fecha']->diffForHumans() }}
                            </span>
                        @endif

                        @can('update', $pub)
                        <div class="post-actions" style="margin-left:auto">
                            <button class="post-actions__trigger" data-actions-trigger aria-label="Opciones">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                                    <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                                </svg>
                            </button>
                            <div class="post-actions__dropdown">
                                <a href="{{ route('publicaciones.edit', $pub) }}" class="post-actions__item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="15" height="15">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Editar zona
                                </a>
                                <form method="POST" action="{{ route('publicaciones.destroy', $pub) }}"
                                      id="delete-feed-{{ $pub->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="post-actions__item post-actions__item--danger"
                                            onclick="document.getElementById('delete-feed-{{ $pub->id }}').submit()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="15" height="15">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                        Eliminar zona
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="profile-empty">
                <p>No hay publicaciones todavía.</p>
                @if($isOwnProfile)
                    <a href="{{ route('publicaciones.create') }}" class="btn btn--primary">Publicar primera zona</a>
                @endif
            </div>
        @endforelse
    </div>
    <div id="tab-replies" class="profile-pane" role="tabpanel">
        @forelse($user->comentarios as $comentario)
            @if($comentario->publicacion)
            <a href="{{ route('publicaciones.show', $comentario->publicacion) }}#respuestas"
               class="reply-card">
                @if($comentario->texto)
                    <p class="reply-card__text">{{ $comentario->texto }}</p>
                @else
                    <p class="reply-card__text" style="opacity:.5;font-style:italic">📷 Imagen adjunta</p>
                @endif
                <p class="reply-card__meta">
                    En <strong>{{ $comentario->publicacion->titulo }}</strong>
                    &middot; {{ $comentario->created_at->diffForHumans() }}
                </p>
            </a>
            @endif
        @empty
            <div class="profile-empty"><p>Sin respuestas todavía.</p></div>
        @endforelse
    </div>
    <div id="tab-likes" class="profile-pane" role="tabpanel">
        @forelse($user->publicacionesLiked as $pub)
            @php $img = $pub->imagenes->first(); @endphp
            <div class="profile-post-card">
                @if($img)
                    <a href="{{ route('publicaciones.show', $pub) }}">
                        <img src="{{ asset('storage/' . $img->ruta) }}"
                             alt="{{ $pub->titulo }}" class="profile-post-card__img">
                    </a>
                @else
                    <div class="profile-post-card__img--placeholder">Sin imagen</div>
                @endif
                <div class="profile-post-card__body">
                    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
                        @if($pub->user->avatar)
                            <img src="{{ asset('storage/' . $pub->user->avatar) }}"
                                 alt="{{ $pub->user->name }}" class="avatar avatar--sm">
                        @else
                            <div class="avatar avatar--sm avatar--placeholder">
                                {{ strtoupper(substr($pub->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <a href="{{ route('usuarios.show', $pub->user) }}"
                           style="font-weight:700;font-size:.875rem;color:#1A1A1A;text-decoration:none">
                            {{ $pub->user->name }}
                        </a>
                        <span style="font-size:.78rem;color:#6B7280;margin-left:auto">{{ $pub->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="profile-post-card__title">
                        <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                    </h3>
                    @if($pub->descripcion)
                        <p class="profile-post-card__desc">{{ $pub->descripcion }}</p>
                    @endif
                    <div class="profile-post-card__footer">
                        <span class="profile-post-card__stat" style="color:var(--c-like)">
                            <svg viewBox="0 0 24 24" fill="currentColor" stroke="none">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            {{ $pub->likes->count() }} me gusta
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="profile-empty"><p>Sin me gustas todavía.</p></div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    var tabs  = document.querySelectorAll('.profile-tab');
    var panes = document.querySelectorAll('.profile-pane');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t)  { t.classList.remove('profile-tab--active'); });
            panes.forEach(function (p) { p.classList.remove('profile-pane--active'); });
            tab.classList.add('profile-tab--active');
            var pane = document.getElementById('tab-' + tab.dataset.tab);
            if (pane) pane.classList.add('profile-pane--active');
        });
    });
}());
</script>
@endpush
