@extends('layouts.app')
@section('title', 'Confirmar contraseña')
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Confirmar contraseña</h1>
        <p>Por seguridad, confirma tu contraseña antes de continuar.</p>
        <form method="POST" action="{{ route('password.confirm') }}" class="form">
            @csrf
            <div class="form__group">
                <label class="form__label" for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
                @error('password')<span class="form__error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn--primary btn--block">Confirmar</button>
        </form>
    </div>
</div>
@endsection
