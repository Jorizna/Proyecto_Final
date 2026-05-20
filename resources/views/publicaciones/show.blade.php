@extends('layouts.app')

@section('title', $publicacion->titulo)

@section('content')
<div class="post-wrapper">
    <article class="post">

        {{-- ── Cabecera ── --}}
        <header class="post__header">
            <a href="{{ route('usuarios.show', $publicacion->user) }}" class="post__avatar-link">
                @if($publicacion->user->avatar)
                    <img src="{{ asset('storage/' . $publicacion->user->avatar) }}"
                         alt="{{ $publicacion->user->name }}" class="avatar avatar--md">
                @else
                    <div class="avatar avatar--md avatar--placeholder">
                        {{ strtoupper(substr($publicacion->user->name, 0, 1)) }}
                    </div>
                @endif
            </a>

            <div class="post__meta">
                <a href="{{ route('usuarios.show', $publicacion->user) }}" class="post__author">
                    {{ $publicacion->user->name }}
                </a>
                <span class="post__fecha">{{ $publicacion->created_at->diffForHumans() }}</span>
                @if($publicacion->etiquetas->isNotEmpty())
                    <div class="tags post__tags">
                        @foreach($publicacion->etiquetas as $et)
                            <span class="tag">{{ $et->nombre }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            @can('update', $publicacion)
                <div class="post__edit-actions">
                    <a href="{{ route('publicaciones.edit', $publicacion) }}"
                       class="btn btn--secondary btn--sm">Editar</a>
                    <form method="POST" action="{{ route('publicaciones.destroy', $publicacion) }}"
                          onsubmit="return confirm('¿Eliminar esta zona?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm">Eliminar</button>
                    </form>
                </div>
            @endcan
        </header>

        {{-- ── Contenido ── --}}
        <div class="post__body">
            <h1 class="post__titulo">{{ $publicacion->titulo }}</h1>
            <p class="post__descripcion">{{ $publicacion->descripcion }}</p>

            @if($publicacion->imagenes->isNotEmpty())
                @php $imgs = $publicacion->imagenes; $n = min($imgs->count(), 4); @endphp
                <div class="fotos-grid fotos-grid--{{ $n }}">
                    @foreach($imgs->take(4) as $img)
                        <div class="fotos-grid__item">
                            <img src="{{ asset('storage/' . $img->ruta) }}" alt="">
                            @can('update', $publicacion)
                                <form method="POST"
                                      action="{{ route('publicaciones.imagenes.destroy', [$publicacion, $img]) }}"
                                      class="fotos-grid__delete">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Eliminar imagen">✕</button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Barra de interacciones ── --}}
        <div class="interactions">
            {{-- Like --}}
            <form method="POST" action="{{ route('likes.toggle', $publicacion) }}">
                @csrf
                <button type="submit"
                        class="int-btn int-btn--like {{ $esLiked ? 'int-btn--active' : '' }}"
                        title="{{ $esLiked ? 'Quitar me gusta' : 'Me gusta' }}">
                    <svg viewBox="0 0 24 24" fill="{{ $esLiked ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span>{{ $publicacion->likes->count() }}</span>
                </button>
            </form>

            {{-- Repost --}}
            <form method="POST" action="{{ route('repostes.toggle', $publicacion) }}">
                @csrf
                <button type="submit"
                        class="int-btn int-btn--repost {{ $esReposteado ? 'int-btn--active' : '' }}"
                        title="{{ $esReposteado ? 'Quitar repost' : 'Repostear' }}">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="17 1 21 5 17 9"/>
                        <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                        <polyline points="7 23 3 19 7 15"/>
                        <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                    </svg>
                    <span>{{ $publicacion->repostes->count() }}</span>
                </button>
            </form>

            <div class="interactions__spacer"></div>

            {{-- Guardar --}}
            <form method="POST" action="{{ route('favoritos.toggle', $publicacion) }}">
                @csrf
                <button type="submit"
                        class="int-btn int-btn--bookmark {{ $esFavorito ? 'int-btn--active' : '' }}"
                        title="{{ $esFavorito ? 'Guardado' : 'Guardar' }}">
                    <svg viewBox="0 0 24 24" fill="{{ $esFavorito ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>{{ $publicacion->favoritos->count() }}</span>
                </button>
            </form>
        </div>

        {{-- ── Respuestas ── --}}
        <div class="replies" id="respuestas">

            {{-- Compositor de respuesta --}}
            @auth
                <form method="POST" action="{{ route('comentarios.store', $publicacion) }}"
                      class="reply-composer" enctype="multipart/form-data" id="reply-form">
                    @csrf
                    <div class="reply-composer__avatar">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name }}" class="avatar avatar--sm">
                        @else
                            <div class="avatar avatar--sm avatar--placeholder">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="reply-composer__content">
                        <textarea name="texto" class="reply-composer__textarea"
                                  placeholder="Escribe tu respuesta..." required maxlength="1000"
                                  rows="2"></textarea>
                        <div id="img-preview" class="reply-preview"></div>
                        <div class="reply-composer__toolbar">
                            <label class="reply-toolbar-btn" title="Adjuntar imágenes (máx. 4)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="18" height="18">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <input type="file" name="imagenes[]" accept="image/*" multiple
                                       class="sr-only" id="reply-imgs" max="4">
                            </label>
                            <span id="reply-imgs-label" class="reply-imgs-label"></span>
                            <button type="submit" class="btn btn--primary btn--sm">Responder</button>
                        </div>
                    </div>
                </form>
            @else
                <p class="replies__login">
                    <a href="{{ route('login') }}">Inicia sesión</a> para responder.
                </p>
            @endauth

            {{-- Lista de respuestas --}}
            @forelse($publicacion->comentarios as $comentario)
                <div class="reply-item">
                    <a href="{{ route('usuarios.show', $comentario->user) }}" class="reply-item__avatar-link">
                        @if($comentario->user->avatar)
                            <img src="{{ asset('storage/' . $comentario->user->avatar) }}"
                                 alt="{{ $comentario->user->name }}" class="avatar avatar--sm">
                        @else
                            <div class="avatar avatar--sm avatar--placeholder">
                                {{ strtoupper(substr($comentario->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </a>
                    <div class="reply-item__body">
                        <div class="reply-item__header">
                            <a href="{{ route('usuarios.show', $comentario->user) }}"
                               class="reply-item__author">{{ $comentario->user->name }}</a>
                            <span class="reply-item__fecha">{{ $comentario->created_at->diffForHumans() }}</span>
                            @if(auth()->id() === $comentario->user_id)
                                <form method="POST"
                                      action="{{ route('comentarios.destroy', [$publicacion, $comentario]) }}"
                                      style="margin-left:auto">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon--danger" title="Eliminar">✕</button>
                                </form>
                            @endif
                        </div>
                        <p class="reply-item__texto">{{ $comentario->texto }}</p>
                        @if($comentario->imagenes->isNotEmpty())
                            @php $ri = $comentario->imagenes; $rn = min($ri->count(), 4); @endphp
                            <div class="fotos-grid fotos-grid--{{ $rn }} fotos-grid--sm">
                                @foreach($ri->take(4) as $rim)
                                    <div class="fotos-grid__item">
                                        <img src="{{ asset('storage/' . $rim->ruta) }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="replies__empty">Sin respuestas todavía. ¡Sé el primero!</p>
            @endforelse
        </div>

    </article>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input  = document.getElementById('reply-imgs');
    const label  = document.getElementById('reply-imgs-label');
    const preview = document.getElementById('img-preview');

    if (!input) return;

    input.addEventListener('change', function () {
        const files = Array.from(this.files).slice(0, 4);
        label.textContent = files.length ? files.length + ' imagen' + (files.length > 1 ? 'es' : '') : '';
        preview.innerHTML = '';
        files.forEach(function (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'reply-preview__thumb';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
}());
</script>
@endpush
