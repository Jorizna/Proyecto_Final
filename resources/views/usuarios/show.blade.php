@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="profile-wrapper">

    {{-- ── Banner ── --}}
    <div class="profile-banner"></div>

    {{-- ── Info del usuario ── --}}
    <div class="profile-info">
        <div class="profile-info__avatar">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}"
                     alt="{{ $user->name }}" class="avatar avatar--xl">
            @else
                <div class="avatar avatar--xl avatar--placeholder">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>

        <div class="profile-info__action">
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

        <h1 class="profile-info__name">{{ $user->name }}</h1>

        @if($user->bio)
            <p class="profile-info__bio">{{ $user->bio }}</p>
        @endif

        <p class="profile-info__since">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14" style="vertical-align:middle">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Miembro desde {{ $user->created_at->format('F Y') }}
        </p>

        <div class="profile-info__stats">
            <span><strong>{{ $seguidos }}</strong> seguidos</span>
            <span><strong>{{ $seguidores }}</strong> seguidores</span>
            <span><strong>{{ $user->publicaciones->count() }}</strong> zonas</span>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="profile-tabs" role="tablist">
        <button class="profile-tab profile-tab--active" data-tab="posts" role="tab">Publicaciones</button>
        <button class="profile-tab" data-tab="replies" role="tab">Respuestas</button>
        <button class="profile-tab" data-tab="likes" role="tab">Me gusta</button>
    </div>

    {{-- ── Tab: Publicaciones + Repostes ── --}}
    <div id="tab-posts" class="profile-pane profile-pane--active" role="tabpanel">
        @forelse($feed as $entry)
            @php $pub = $entry['item']; @endphp

            @if($entry['tipo'] === 'reposte')
                <div class="feed-repost-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="13" height="13">
                        <polyline points="17 1 21 5 17 9"/>
                        <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                        <polyline points="7 23 3 19 7 15"/>
                        <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                    </svg>
                    Reposteado
                </div>
            @endif

            <article class="feed-item">
                <div class="feed-item__thumb">
                    @php $img = $pub->imagenes->first(); @endphp
                    @if($img)
                        <img src="{{ asset('storage/' . $img->ruta) }}"
                             alt="{{ $pub->titulo }}" class="feed-item__img">
                    @else
                        <div class="feed-item__img feed-item__img--empty"></div>
                    @endif
                </div>
                <div class="feed-item__content">
                    <div class="feed-item__meta">
                        @if($entry['tipo'] === 'reposte')
                            <span class="feed-item__autor">{{ $pub->user->name }}</span> &middot;
                        @endif
                        <span class="feed-item__fecha">{{ $entry['fecha']->diffForHumans() }}</span>
                    </div>
                    <h3 class="feed-item__title">
                        <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                    </h3>
                    <p class="feed-item__desc">{{ mb_strimwidth($pub->descripcion, 0, 120, '...') }}</p>
                    <div class="feed-item__stats">
                        <span class="feed-stat feed-stat--like">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            {{ $pub->likes->count() }}
                        </span>
                        <span class="feed-stat">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <polyline points="17 1 21 5 17 9"/>
                                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                                <polyline points="7 23 3 19 7 15"/>
                                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                            </svg>
                            {{ $pub->repostes->count() }}
                        </span>
                        @can('update', $pub)
                            <a href="{{ route('publicaciones.edit', $pub) }}"
                               class="btn btn--secondary btn--sm">Editar</a>
                            <form method="POST" action="{{ route('publicaciones.destroy', $pub) }}"
                                  onsubmit="return confirm('¿Eliminar esta zona?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn--danger btn--sm">Eliminar</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </article>
        @empty
            <div class="profile-empty">
                <p>No hay publicaciones todavía.</p>
                @if($isOwnProfile)
                    <a href="{{ route('publicaciones.create') }}" class="btn btn--primary">Publicar primera zona</a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- ── Tab: Respuestas ── --}}
    <div id="tab-replies" class="profile-pane" role="tabpanel">
        @forelse($user->comentarios as $comentario)
            <a href="{{ route('publicaciones.show', $comentario->publicacion) }}#respuestas"
               class="reply-card">
                <p class="reply-card__text">{{ $comentario->texto }}</p>
                <p class="reply-card__meta">
                    En <strong>{{ $comentario->publicacion->titulo }}</strong>
                    &middot; {{ $comentario->created_at->diffForHumans() }}
                </p>
            </a>
        @empty
            <div class="profile-empty"><p>Sin respuestas todavía.</p></div>
        @endforelse
    </div>

    {{-- ── Tab: Me gusta ── --}}
    <div id="tab-likes" class="profile-pane" role="tabpanel">
        @forelse($user->publicacionesLiked as $pub)
            <article class="feed-item">
                <div class="feed-item__thumb">
                    @php $img = $pub->imagenes->first(); @endphp
                    @if($img)
                        <img src="{{ asset('storage/' . $img->ruta) }}"
                             alt="{{ $pub->titulo }}" class="feed-item__img">
                    @else
                        <div class="feed-item__img feed-item__img--empty"></div>
                    @endif
                </div>
                <div class="feed-item__content">
                    <div class="feed-item__meta">
                        <span class="feed-item__autor">{{ $pub->user->name }}</span>
                    </div>
                    <h3 class="feed-item__title">
                        <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                    </h3>
                    <p class="feed-item__desc">{{ mb_strimwidth($pub->descripcion, 0, 120, '...') }}</p>
                    <div class="feed-item__stats">
                        <span class="feed-stat feed-stat--like feed-stat--filled">
                            <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            {{ $pub->likes->count() }}
                        </span>
                    </div>
                </div>
            </article>
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
