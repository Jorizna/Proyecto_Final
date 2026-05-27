@extends('layouts.app')

@section('title', $tutorial->titulo)

@section('content')
<div class="post-wrapper" style="max-width:680px">
    <article class="post">
        @if($tutorial->imagen_cabecera)
            <div style="overflow:hidden;border-radius:var(--r-xl) var(--r-xl) 0 0">
                <img src="{{ asset('storage/' . $tutorial->imagen_cabecera) }}"
                     alt="{{ $tutorial->titulo }}"
                     style="width:100%;height:240px;object-fit:cover;display:block">
            </div>
        @endif

        <div class="post__header">
            <a href="{{ route('usuarios.show', $tutorial->user) }}" class="post__avatar-link">
                @if($tutorial->user->avatar)
                    <img src="{{ asset('storage/' . $tutorial->user->avatar) }}"
                         alt="{{ $tutorial->user->name }}" class="avatar avatar--md">
                @else
                    <div class="avatar avatar--md avatar--placeholder">
                        {{ strtoupper(substr($tutorial->user->name, 0, 1)) }}
                    </div>
                @endif
            </a>
            <div class="post__meta">
                <a href="{{ route('usuarios.show', $tutorial->user) }}" class="post__author">
                    {{ $tutorial->user->name }}
                </a>
                <span class="post__fecha">{{ $tutorial->created_at->diffForHumans() }}</span>
                <div class="tags post__tags" style="margin-top:.2rem">
                    <span class="tag ugc-tag--{{ $tutorial->categoria }}">{{ $tutorial->categoriaLabel() }}</span>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;margin-left:auto">
                <a href="{{ route('guias.index') }}" class="btn btn--secondary btn--sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                    Guías
                </a>
                @auth
                    @if(auth()->id() === $tutorial->user_id)
                        <div class="post-actions" style="position:relative">
                            <button class="post-actions__trigger" id="tutorial-actions-trigger" aria-label="Opciones">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                                    <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                                </svg>
                            </button>
                            <div class="post-actions__dropdown" id="tutorial-actions-dropdown">
                                <form method="POST" action="{{ route('tutoriales.destroy', $tutorial) }}"
                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este tutorial? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="post-actions__item post-actions__item--danger">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="15" height="15">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                        Eliminar tutorial
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <div class="post__body">
            <h1 class="post__titulo">{{ $tutorial->titulo }}</h1>
            <div class="tutorial-content">
                {!! nl2br(e($tutorial->contenido)) !!}
            </div>
        </div>

        @push('scripts')
        <script>
        (function () {
            var trigger  = document.getElementById('tutorial-actions-trigger');
            var dropdown = document.getElementById('tutorial-actions-dropdown');
            if (!trigger || !dropdown) return;
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('is-open');
            });
            document.addEventListener('click', function () {
                dropdown.classList.remove('is-open');
            });
        }());
        </script>
        @endpush

        <div class="interactions" style="padding:.875rem 1.5rem">
            <div style="font-size:.8rem;color:var(--c-text-2);display:flex;align-items:center;gap:.5rem">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="14" height="14">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Publicado {{ $tutorial->created_at->format('d M Y') }}
            </div>
            <div class="interactions__spacer"></div>
            <a href="{{ route('guias.index') }}" class="btn btn--secondary btn--sm">Ver todas las guías</a>
        </div>

    </article>
</div>
@endsection
