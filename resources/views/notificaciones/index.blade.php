@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="post-wrapper" style="max-width:640px">

    <div class="notif-header">
        <h1 class="notif-header__title">Notificaciones</h1>
        @if($notificaciones->isNotEmpty())
            <form method="POST" action="{{ route('notificaciones.read') }}">
                @csrf
                <button type="submit" class="notif-mark-btn">Marcar todas como leídas</button>
            </form>
        @endif
    </div>

    @if($notificaciones->isEmpty())
        <div class="notif-empty">
            <div class="notif-empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
            <p class="notif-empty__title">Sin notificaciones</p>
            <p class="notif-empty__sub">Aquí aparecerán los likes, respuestas y guardados que recibas en tus zonas.</p>
        </div>
    @else
        <div class="notif-feed">
            @foreach($notificaciones as $notif)
                <a href="{{ $notif->enlace() }}" class="notif-item{{ !$notif->leida ? ' notif-item--unread' : '' }}">
                    <div class="notif-item__dot-wrap">
                        @if(!$notif->leida)
                            <span class="notif-item__dot"></span>
                        @endif
                    </div>
                    <div class="notif-item__avatar-col">
                        <div class="notif-item__avatar-wrap">
                            @if($notif->actor && $notif->actor->avatar)
                                <img src="{{ asset('storage/' . $notif->actor->avatar) }}"
                                     alt="{{ $notif->actor->name }}"
                                     class="avatar avatar--md">
                            @else
                                <div class="avatar avatar--md avatar--placeholder">
                                    {{ $notif->actor ? strtoupper(substr($notif->actor->name, 0, 1)) : '?' }}
                                </div>
                            @endif
                            <div class="notif-item__type-badge notif-item__type-badge--{{ $notif->tipo }}">
                                @if($notif->tipo === 'like')
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="10" height="10">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                @elseif($notif->tipo === 'comentario')
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="10" height="10">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                @elseif($notif->tipo === 'favorito')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="10" height="10">
                                        <polyline points="17 1 21 5 17 9"/>
                                        <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                                        <polyline points="7 23 3 19 7 15"/>
                                        <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                                    </svg>
                                @elseif($notif->tipo === 'seguir')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="10" height="10">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <line x1="19" y1="8" x2="19" y2="14"/>
                                        <line x1="22" y1="11" x2="16" y2="11"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="10" height="10">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="notif-item__body">
                        <p class="notif-item__text">{!! $notif->textoHtml() !!}</p>
                        <span class="notif-item__time">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>

                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
