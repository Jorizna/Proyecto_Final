@extends('layouts.app')

@section('title', 'Editar perfil')

@section('content')
<div class="form-page">
    <h1 class="page-header__title">Editar perfil</h1>

    <form method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data" class="form">
        @csrf @method('PUT')

        {{-- Banner --}}
        <div class="form__group">
            <label class="form__label">Imagen de portada</label>
            <img id="banner-preview" src="{{ $user->banner ? asset('storage/' . $user->banner) : '' }}"
                 alt="Vista previa de portada"
                 style="width:100%;height:120px;object-fit:cover;border-radius:12px;margin-bottom:.5rem;display:{{ $user->banner ? 'block' : 'none' }}">
            <label class="upload-btn" style="display:inline-flex">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="16" height="16">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                {{ $user->banner ? 'Cambiar portada' : 'Subir portada' }}
                <input type="file" id="banner-input" name="banner" accept="image/jpeg,image/png,image/webp">
            </label>
            @error('banner')<span class="form__error" style="display:block;margin-top:.35rem">{{ $message }}</span>@enderror
        </div>

        {{-- Avatar --}}
        <div class="form__group form__group--avatar">
            <div id="avatar-current">
                @if($user->avatar)
                    <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar actual" class="avatar avatar--md">
                @else
                    <div id="avatar-placeholder" class="avatar avatar--md avatar--placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <img id="avatar-preview" src="" alt="Vista previa" class="avatar avatar--md" style="display:none">
                @endif
            </div>
            <div>
                <label class="form__label" for="avatar">Cambiar foto de perfil</label>
                <label class="upload-btn" style="display:inline-flex;margin-top:.35rem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="16" height="16">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Cambiar avatar
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                </label>
                @error('avatar')<span class="form__error" style="display:block;margin-top:.35rem">{{ $message }}</span>@enderror
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

@push('scripts')
<script>
(function () {
    function previewFile(input, previewId, placeholderId) {
        input.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholderId) {
                    var ph = document.getElementById(placeholderId);
                    if (ph) ph.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        });
    }

    var bannerInput  = document.getElementById('banner-input');
    var avatarInput  = document.getElementById('avatar');
    if (bannerInput) previewFile(bannerInput, 'banner-preview', null);
    if (avatarInput) previewFile(avatarInput, 'avatar-preview', 'avatar-placeholder');
}());
</script>
@endpush
