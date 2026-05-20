@extends('layouts.app')

@section('title', 'Editar perfil')

@section('content')
<div class="form-page">
    <h1 class="page-header__title">Editar perfil</h1>

    <form method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data" class="form">
        @csrf @method('PUT')

        <div class="form__group form__group--avatar">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar actual" class="avatar avatar--md">
            @else
                <div class="avatar avatar--md avatar--placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
            <div>
                <label class="form__label" for="avatar">Cambiar foto de perfil</label>
                <input type="file" id="avatar" name="avatar" class="form-control"
                       accept="image/jpeg,image/png,image/webp">
                @error('avatar')<span class="form__error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form__group">
            <label class="form__label" for="name">Nombre</label>
            <input type="text" id="name" name="name"
                   class="form-control @error('name') form-control--error @enderror"
                   value="{{ old('name', $user->name) }}" required maxlength="255">
            @error('name')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="email">Email</label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') form-control--error @enderror"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="bio">Biografía</label>
            <textarea id="bio" name="bio" rows="3" maxlength="500"
                      class="form-control @error('bio') form-control--error @enderror"
                      placeholder="Cuéntanos algo sobre ti...">{{ old('bio', $user->bio) }}</textarea>
            @error('bio')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__actions">
            <a href="{{ route('perfil.show') }}" class="btn btn--secondary">Cancelar</a>
            <button type="submit" class="btn btn--primary">Guardar cambios</button>
        </div>
    </form>

    <hr class="divider">

    <h2 class="section__title">Cambiar contraseña</h2>
    <form method="POST" action="{{ route('perfil.password') }}" class="form">
        @csrf @method('PUT')

        <div class="form__group">
            <label class="form__label" for="current_password">Contraseña actual</label>
            <input type="password" id="current_password" name="current_password"
                   class="form-control @error('current_password') form-control--error @enderror" required>
            @error('current_password')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="password">Nueva contraseña</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') form-control--error @enderror"
                   required minlength="8">
            @error('password')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="password_confirmation">Confirmar contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" required>
        </div>

        <div class="form__actions">
            <button type="submit" class="btn btn--primary">Cambiar contraseña</button>
        </div>
    </form>
</div>
@endsection
