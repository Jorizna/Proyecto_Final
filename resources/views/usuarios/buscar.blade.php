@extends('layouts.app')

@section('title', 'Buscar usuarios')

@section('content')
<div class="post-wrapper" style="max-width:640px">

    <div style="margin-bottom:1.5rem">
        <h1 style="font-size:1.4rem;font-weight:800;letter-spacing:-.04em;margin-bottom:.25rem">Buscar usuarios</h1>
        @if($q)
            <p style="font-size:.875rem;color:var(--c-text-2)">
                {{ $usuarios->count() }} resultado{{ $usuarios->count() !== 1 ? 's' : '' }} para
                <strong>{{ $q }}</strong>
            </p>
        @else
            <p style="font-size:.875rem;color:var(--c-text-2)">Introduce un nombre en el buscador de la barra lateral.</p>
        @endif
    </div>

    @if($usuarios->isEmpty() && $q)
        <div class="notif-empty" style="padding:2.5rem 0">
            <div class="notif-empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <p class="notif-empty__title">Sin resultados</p>
            <p class="notif-empty__sub">No hay ningún usuario con ese nombre.</p>
        </div>
    @else
        <div class="user-search-list">
            @foreach($usuarios as $u)
                <a href="{{ route('usuarios.show', $u) }}" class="user-search-row">
                    <div class="user-search-row__avatar">
                        @if($u->avatar)
                            <img src="{{ asset('storage/' . $u->avatar) }}"
                                 alt="{{ $u->name }}" class="avatar avatar--md">
                        @else
                            <div class="avatar avatar--md avatar--placeholder">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="user-search-row__info">
                        <span class="user-search-row__name">{{ $u->name }}</span>
                        @if($u->bio)
                            <span class="user-search-row__bio">{{ Str::limit($u->bio, 80) }}</span>
                        @endif
                    </div>
                    <svg class="user-search-row__arrow" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" width="16" height="16">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
