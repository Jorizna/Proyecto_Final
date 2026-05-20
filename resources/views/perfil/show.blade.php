@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="perfil-tw">

    {{-- Banner --}}
    <div class="perfil-tw__banner"></div>

    {{-- Info del usuario --}}
    <div class="perfil-tw__info">
        <div class="perfil-tw__avatar-wrap">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar avatar--xl">
            @else
                <div class="avatar avatar--xl avatar--placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
        </div>
        <div class="perfil-tw__top-row">
            <a href="{{ route('perfil.edit') }}" class="btn btn--secondary btn--sm">Editar perfil</a>
        </div>
        <h1 class="perfil-tw__nombre">{{ $user->name }}</h1>
        <p class="perfil-tw__email">{{ $user->email }}</p>
        <p class="perfil-tw__fecha">Miembro desde {{ $user->created_at->format('F Y') }}</p>
        <div class="perfil-tw__stats">
            <span><strong>{{ $user->publicaciones->count() }}</strong> zonas</span>
            <span><strong>{{ $user->publicacionesLiked->count() }}</strong> me gusta</span>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="perfil-tw__tabs">
        <button class="tab-btn tab-btn--active" data-tab="posts">Publicaciones</button>
        <button class="tab-btn" data-tab="respuestas">Respuestas</button>
        <button class="tab-btn" data-tab="likes">Me gusta</button>
    </div>

    {{-- Tab 1: Publicaciones propias + repostes --}}
    <div id="tab-posts" class="tab-pane tab-pane--active">
        @forelse($feed as $entry)
            @php $pub = $entry['item']; @endphp
            @if($entry['tipo'] === 'reposte')
                <div class="feed-repost-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="13" height="13">
                        <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                        <polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                    </svg>
                    Reposteado
                </div>
            @endif
            <article class="feed-card">
                <div class="feed-card__img-wrap">
                    @php $img = $pub->imagenes->first(); @endphp
                    @if($img)
                        <img src="{{ asset('storage/' . $img->ruta) }}" alt="{{ $pub->titulo }}" class="feed-card__img">
                    @else
                        <div class="feed-card__img feed-card__img--none"></div>
                    @endif
                </div>
                <div class="feed-card__body">
                    <div class="feed-card__meta">
                        @if($entry['tipo'] === 'reposte')
                            <span class="feed-card__autor">{{ $pub->user->name }}</span> ·
                        @endif
                        <span>{{ $entry['fecha']->diffForHumans() }}</span>
                    </div>
                    <h3 class="feed-card__titulo">
                        <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                    </h3>
                    <p class="feed-card__desc">{{ mb_strimwidth($pub->descripcion, 0, 120, '...') }}</p>
                    <div class="feed-card__stats">
                        <span>
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            {{ $pub->likes->count() }}
                        </span>
                        <span>
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                                <polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                            </svg>
                            {{ $pub->repostes->count() }}
                        </span>
                        @can('update', $pub)
                            <a href="{{ route('publicaciones.edit', $pub) }}" class="btn btn--secondary btn--sm">Editar</a>
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
            <div class="empty-state">
                <p>Todavía no has publicado ni reposteado nada.</p>
                <a href="{{ route('publicaciones.create') }}" class="btn btn--primary">Publicar primera zona</a>
            </div>
        @endforelse
    </div>

    {{-- Tab 2: Respuestas --}}
    <div id="tab-respuestas" class="tab-pane">
        @forelse($user->comentarios as $comentario)
            <a href="{{ route('publicaciones.show', $comentario->publicacion) }}#respuestas"
               class="respuesta-card">
                <div class="respuesta-card__texto">{{ $comentario->texto }}</div>
                <div class="respuesta-card__meta">
                    En <strong>{{ $comentario->publicacion->titulo }}</strong>
                    · {{ $comentario->created_at->diffForHumans() }}
                </div>
                @if($comentario->imagen)
                    <img src="{{ asset('storage/' . $comentario->imagen) }}"
                         alt="" class="respuesta-card__img">
                @endif
            </a>
        @empty
            <div class="empty-state"><p>Todavía no has respondido ninguna publicación.</p></div>
        @endforelse
    </div>

    {{-- Tab 3: Me gusta --}}
    <div id="tab-likes" class="tab-pane">
        @forelse($user->publicacionesLiked as $pub)
            <article class="feed-card">
                <div class="feed-card__img-wrap">
                    @php $img = $pub->imagenes->first(); @endphp
                    @if($img)
                        <img src="{{ asset('storage/' . $img->ruta) }}" alt="{{ $pub->titulo }}" class="feed-card__img">
                    @else
                        <div class="feed-card__img feed-card__img--none"></div>
                    @endif
                </div>
                <div class="feed-card__body">
                    <div class="feed-card__meta">
                        <span class="feed-card__autor">{{ $pub->user->name }}</span>
                    </div>
                    <h3 class="feed-card__titulo">
                        <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                    </h3>
                    <p class="feed-card__desc">{{ mb_strimwidth($pub->descripcion, 0, 120, '...') }}</p>
                    <div class="feed-card__stats">
                        <span>
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            {{ $pub->likes->count() }}
                        </span>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state"><p>Todavía no has dado me gusta a ninguna publicación.</p></div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('tab-btn--active'); });
        document.querySelectorAll('.tab-pane').forEach(function (p) { p.classList.remove('tab-pane--active'); });
        btn.classList.add('tab-btn--active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('tab-pane--active');
    });
});
</script>
@endpush
