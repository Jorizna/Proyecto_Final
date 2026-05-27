@extends('layouts.app')

@section('title', 'Iniciar sesión')
@section('body-class', 'auth-bg')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Iniciar sesión</h1>
        <p class="auth-card__subtitle">Accede a FishSpot</p>

        <form method="POST" action="{{ route('login') }}" class="form">
            @csrf

            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') form-control--error @enderror"
                       value="{{ $errors->has('email') ? old('email') : '' }}" required autofocus autocomplete="username">
                @error('email')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <div class="form__group">
                <label class="form__label" for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') form-control--error @enderror"
                       required autocomplete="current-password">
                @error('password')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <div class="form__group form__group--inline">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Recordarme
                </label>
            </div>

            <button type="submit" class="btn btn--primary btn--block">Entrar</button>
        </form>

        <div class="auth-http-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Entorno local (HTTP). Tu contraseña se almacena cifrada con Bcrypt.
            <a href="{{ route('privacidad') }}" class="link">Política de privacidad</a>
        </div>

        <p class="auth-card__footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}" class="link">Regístrate</a>
        </p>
    </div>
</div>
@endsection
