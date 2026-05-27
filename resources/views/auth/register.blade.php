@extends('layouts.app')

@section('title', 'Registrarse')
@section('body-class', 'auth-bg')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Crear cuenta</h1>
        <p class="auth-card__subtitle">Únete a la comunidad de pescadores de España</p>

        <form method="POST" action="{{ route('register') }}" class="form">
            @csrf

            <div class="form__group">
                <label class="form__label" for="name">Nombre</label>
                <input type="text" id="name" name="name" class="form-control @error('name') form-control--error @enderror"
                       value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') form-control--error @enderror"
                       value="{{ old('email') }}" required autocomplete="username">
                @error('email')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <div class="form__group">
                <label class="form__label" for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') form-control--error @enderror"
                       required autocomplete="new-password" minlength="8">
                @error('password')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <div class="form__group">
                <label class="form__label" for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" required autocomplete="new-password">
            </div>

            <div class="form__group">
                <label class="checkbox-label checkbox-label--terms">
                    <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                    <span>He leído y acepto la
                        <a href="{{ route('privacidad') }}" target="_blank" class="link">Política de Privacidad y Términos de Uso</a>
                    </span>
                </label>
                @error('terms')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn btn--primary btn--block">Registrarse</button>
        </form>

        <p class="auth-card__footer">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="link">Inicia sesión</a>
        </p>
    </div>
</div>
@endsection
