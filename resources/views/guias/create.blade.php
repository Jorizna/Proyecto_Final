@extends('layouts.app')

@section('title', 'Añadir Tutorial')

@section('content')
<div class="post-wrapper" style="max-width:660px">

    <div class="card" style="padding:2rem 2rem 2.25rem">

        <div style="margin-bottom:1.75rem">
            <a href="{{ route('guias.index') }}" class="btn btn--secondary btn--sm" style="margin-bottom:1rem">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Volver a Guías
            </a>
            <h1 style="font-size:1.5rem;font-weight:800;letter-spacing:-.04em;line-height:1.15;margin-bottom:.375rem">
                Publicar Tutorial
            </h1>
            <p style="font-size:.875rem;color:var(--c-text-2)">Comparte tu conocimiento con la comunidad de pescadores de España.</p>
        </div>

        <form method="POST" action="{{ route('tutoriales.store') }}" enctype="multipart/form-data" class="form">
            @csrf

            <div class="form__group">
                <label class="form__label" for="titulo">Título del tutorial</label>
                <input type="text" id="titulo" name="titulo" maxlength="160"
                       class="form-control @error('titulo') form-control--error @enderror"
                       value="{{ old('titulo') }}" required
                       placeholder="Ej: Técnica de jigging vertical para Black Bass en verano">
                @error('titulo')<span class="form__error">{{ $message }}</span>@enderror
                <span class="form__hint">Máximo 160 caracteres. Sé descriptivo y específico.</span>
            </div>

            <div class="form__group">
                <label class="form__label" for="categoria">Categoría</label>
                <select id="categoria" name="categoria"
                        class="form-control @error('categoria') form-control--error @enderror" required>
                    <option value="" disabled {{ old('categoria') ? '' : 'selected' }}>Selecciona una categoría...</option>
                    <option value="tecnica"  {{ old('categoria') === 'tecnica'  ? 'selected' : '' }}>Técnica de Pesca</option>
                    <option value="equipo"   {{ old('categoria') === 'equipo'   ? 'selected' : '' }}>Equipamiento y Material</option>
                    <option value="entorno"  {{ old('categoria') === 'entorno'  ? 'selected' : '' }}>Entorno y Localización</option>
                </select>
                @error('categoria')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <div class="form__group">
                <label class="form__label" for="contenido">Contenido del tutorial</label>
                <textarea id="contenido" name="contenido" rows="14" maxlength="10000"
                          class="form-control @error('contenido') form-control--error @enderror"
                          required placeholder="Describe tu técnica o consejo en detalle. Explica el material necesario, los pasos a seguir, las especies objetivo y cualquier tip que hayas aprendido con la experiencia...">{{ old('contenido') }}</textarea>
                @error('contenido')<span class="form__error">{{ $message }}</span>@enderror
                <span class="form__hint">Máximo 10 000 caracteres. Sé claro y detallado — la comunidad lo agradecerá.</span>
            </div>

            <div class="form__group">
                <label class="form__label">Imagen de cabecera <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--c-text-2)">(opcional)</span></label>
                <label class="upload-btn" for="imagen" style="display:inline-flex">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="15" height="15">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    Seleccionar imagen
                    <input type="file" id="imagen" name="imagen" accept="image/*" onchange="previewImg(this)">
                </label>
                <span class="form__hint">JPG, PNG o WEBP · Máx. 4 MB. Se mostrará en la tarjeta del tutorial.</span>
                @error('imagen')<span class="form__error">{{ $message }}</span>@enderror
                <div id="img-preview-wrap" style="margin-top:.625rem;display:none">
                    <img id="img-preview-thumb" src="" alt="Vista previa"
                         style="max-height:180px;border-radius:var(--r-md);border:var(--border);object-fit:cover;display:block">
                </div>
            </div>

            <div class="form__actions" style="margin-top:.5rem">
                <button type="submit" class="btn btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" width="15" height="15">
                        <path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/>
                    </svg>
                    Publicar tutorial
                </button>
                <a href="{{ route('guias.index') }}" class="btn btn--secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImg(input) {
    var wrap  = document.getElementById('img-preview-wrap');
    var thumb = document.getElementById('img-preview-thumb');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            thumb.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        wrap.style.display = 'none';
    }
}
</script>
@endsection
