@extends('layouts.app')

@section('title', 'Nueva zona de pesca')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapa-selector { height: 320px; border-radius: 10px; cursor: crosshair; }
    #mapa-selector .leaflet-container { cursor: crosshair; }
</style>
@endpush

@section('content')
<div class="form-page">
    <h1 class="page-header__title">Nueva zona de pesca</h1>
    <p class="page-header__subtitle">Comparte una zona de pesca con la comunidad</p>

    <form method="POST" action="{{ route('publicaciones.store') }}" enctype="multipart/form-data" class="form">
        @csrf

        <div class="form__group">
            <label class="form__label" for="titulo">Título *</label>
            <input type="text" id="titulo" name="titulo" class="form-control @error('titulo') form-control--error @enderror"
                   value="{{ old('titulo') }}" placeholder="Ej: Río Gállego en Zuera" required maxlength="255">
            @error('titulo')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="descripcion">Descripción *</label>
            <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') form-control--error @enderror"
                      rows="5" placeholder="Describe la zona: acceso, especies, mejores épocas..." required maxlength="2000">{{ old('descripcion') }}</textarea>
            @error('descripcion')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label">Ubicación en el mapa *</label>
            <p class="form__hint">Haz clic en el mapa para seleccionar las coordenadas exactas</p>
            <div id="mapa-selector"></div>
            <div class="coords-row">
                <div class="form__group">
                    <label class="form__label" for="latitud">Latitud</label>
                    <input type="number" id="latitud" name="latitud" class="form-control @error('latitud') form-control--error @enderror"
                           value="{{ old('latitud') }}" step="any" min="-90" max="90" required readonly
                           placeholder="Haz clic en el mapa">
                    @error('latitud')<span class="form__error">{{ $message }}</span>@enderror
                </div>
                <div class="form__group">
                    <label class="form__label" for="longitud">Longitud</label>
                    <input type="number" id="longitud" name="longitud" class="form-control @error('longitud') form-control--error @enderror"
                           value="{{ old('longitud') }}" step="any" min="-180" max="180" required readonly
                           placeholder="Haz clic en el mapa">
                    @error('longitud')<span class="form__error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <label class="form__label">Especies / Etiquetas</label>
            <div class="checkboxes-grid">
                @foreach($etiquetas as $etiqueta)
                    <label class="checkbox-label">
                        <input type="checkbox" name="etiquetas[]" value="{{ $etiqueta->id }}"
                               {{ in_array($etiqueta->id, old('etiquetas', [])) ? 'checked' : '' }}>
                        {{ $etiqueta->nombre }}
                    </label>
                @endforeach
            </div>
            @error('etiquetas')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="imagenes">Imágenes (máx. 5, hasta 5MB cada una)</label>
            <input type="file" id="imagenes" name="imagenes[]" class="form-control"
                   accept="image/jpeg,image/png,image/webp" multiple>
            <div id="preview-imagenes" class="preview-grid"></div>
            @error('imagenes')<span class="form__error">{{ $message }}</span>@enderror
            @error('imagenes.*')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__actions">
            <a href="{{ route('publicaciones.index') }}" class="btn btn--secondary">Cancelar</a>
            <button type="submit" class="btn btn--primary">Publicar zona</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Mapa selector
    const mapa = L.map('mapa-selector').setView([41.6, -0.9], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 18,
    }).addTo(mapa);

    let marker = null;

    // Restaurar posición previa si existe (error de validación)
    const latPrev = '{{ old('latitud') }}';
    const lngPrev = '{{ old('longitud') }}';
    if (latPrev && lngPrev) {
        const latlng = { lat: parseFloat(latPrev), lng: parseFloat(lngPrev) };
        marker = L.marker([latlng.lat, latlng.lng]).addTo(mapa);
        mapa.setView([latlng.lat, latlng.lng], 13);
    }

    mapa.on('click', e => {
        const { lat, lng } = e.latlng;

        document.getElementById('latitud').value = lat.toFixed(7);
        document.getElementById('longitud').value = lng.toFixed(7);

        if (marker) marker.remove();
        marker = L.marker([lat, lng]).addTo(mapa);
    });

    // Preview de imágenes
    document.getElementById('imagenes').addEventListener('change', function() {
        const preview = document.getElementById('preview-imagenes');
        preview.innerHTML = '';
        Array.from(this.files).slice(0, 5).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-img';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush
