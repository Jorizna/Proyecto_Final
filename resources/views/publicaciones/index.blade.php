@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="feed-header">
    <p class="feed-header__eyebrow">FishSpot · España</p>
    <h1 class="feed-header__title">Zonas de Pesca</h1>
    <p class="feed-header__sub">{{ $publicaciones->total() }} zonas publicadas por la comunidad</p>
</div>

@if($publicaciones->isEmpty())
    <div class="empty-state">
        <p>Todavía no hay zonas publicadas.</p>
        <a href="{{ route('publicaciones.create') }}" class="btn btn--primary">Sé el primero en publicar</a>
    </div>
@else
    <div class="feed-cascade">
        @foreach($publicaciones as $pub)
            @php $primeraImagen = $pub->imagenes->first(); @endphp
            <article class="post-card">
                <div class="post-card__body">
                    <div class="post-card__header">
                        <a href="{{ route('usuarios.show', $pub->user) }}" class="post-card__author-link">
                            @if($pub->user->avatar)
                                <img src="{{ asset('storage/' . $pub->user->avatar) }}"
                                     alt="{{ $pub->user->name }}" class="avatar avatar--sm">
                            @else
                                <div class="avatar avatar--sm avatar--placeholder">
                                    {{ strtoupper(substr($pub->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="post-card__author">{{ $pub->user->name }}</span>
                        </a>
                        <span class="post-card__date">{{ $pub->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="post-card__inner">
                        <div class="post-card__text">
                            <h3 class="post-card__title">
                                <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                            </h3>
                            @if($pub->descripcion)
                                <p class="post-card__desc">{{ $pub->descripcion }}</p>
                            @endif
                            @if($pub->etiquetas->isNotEmpty())
                                <div class="post-card__tags tags">
                                    @foreach($pub->etiquetas->take(3) as $etiqueta)
                                        <span class="tag">{{ $etiqueta->nombre }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if($primeraImagen)
                            <a href="{{ route('publicaciones.show', $pub) }}" class="post-card__thumb">
                                <img src="{{ asset('storage/' . $primeraImagen->ruta) }}"
                                     alt="{{ $pub->titulo }}" class="post-card__thumb-img">
                            </a>
                        @endif
                    </div>
                </div>

                <div class="post-card__footer">
                    <span class="post-card__stat post-card__stat--like">
                        <svg viewBox="0 0 24 24" fill="currentColor" stroke="none">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        {{ $pub->likes->count() }}
                    </span>
                    <span class="post-card__stat">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        {{ $pub->comentarios->count() }}
                    </span>
                    <a href="{{ route('publicaciones.show', $pub) }}" class="post-card__see-more">
                        Ver zona →
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    {{ $publicaciones->links('pagination.fishspot') }}
@endif
@endsection
