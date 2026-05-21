@extends('layouts.app')

@section('title', 'Iniciar sesión')
@section('body-class', 'auth-bg')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Iniciar sesión</h1>
        <p class="auth-card__subtitle">Accede a FishSpot Aragón</p>

        <form method="POST" action="{{ route('login') }}" class="form">
            @csrf

            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') form-control--error @enderror"
                       value="{{ old('email') }}" required autofocus autocomplete="username">
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
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link">¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            <button type="submit" class="btn btn--primary btn--block">Entrar</button>
        </form>

        <p class="auth-card__footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}" class="link">Regístrate</a>
        </p>
    </div>
</div>
@endsection
