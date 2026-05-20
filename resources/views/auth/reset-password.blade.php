@extends('layouts.app')
@section('title', 'Nueva contraseña')
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Nueva contraseña</h1>
        <form method="POST" action="{{ route('password.store') }}" class="form">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required>
                @error('email')<span class="form__error">{{ $message }}</span>@enderror
            </div>
            <div class="form__group">
                <label class="form__label" for="password">Nueva contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
                @error('password')<span class="form__error">{{ $message }}</span>@enderror
            </div>
            <div class="form__group">
                <label class="form__label" for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn--primary btn--block">Restablecer contraseña</button>
        </form>
    </div>
</div>
@endsection
