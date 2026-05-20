@extends('layouts.app')
@section('title', 'Verificar email')
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Verificar email</h1>
        <p>Por favor, verifica tu dirección de email haciendo clic en el enlace que te hemos enviado.</p>
        @if (session('status') == 'verification-link-sent')
            <div class="alert alert--success">Se ha enviado un nuevo enlace de verificación.</div>
        @endif
        <form method="POST" action="{{ route('verification.send') }}" class="form">
            @csrf
            <button type="submit" class="btn btn--primary btn--block">Reenviar email de verificación</button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="form" style="margin-top:1rem">
            @csrf
            <button type="submit" class="btn btn--secondary btn--block">Cerrar sesión</button>
        </form>
    </div>
</div>
@endsection
