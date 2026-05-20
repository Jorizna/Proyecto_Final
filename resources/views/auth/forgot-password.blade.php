@extends('layouts.app')
@section('title', 'Recuperar contraseña')
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Recuperar contraseña</h1>
        @if (session('status'))
            <div class="alert alert--success">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('password.email') }}" class="form">
            @csrf
            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                @error('email')<span class="form__error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn--primary btn--block">Enviar enlace de recuperación</button>
        </form>
        <p class="auth-card__footer"><a href="{{ route('login') }}" class="link">Volver al inicio de sesión</a></p>
    </div>
</div>
@endsection
