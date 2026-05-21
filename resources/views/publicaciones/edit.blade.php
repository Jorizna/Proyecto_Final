@extends('layouts.app')

@section('title', 'Editar zona')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapa-selector { height: 280px; border-radius: 10px; cursor: crosshair; }
</style>
@endpush

@section('content')
<div class="form-page">
    <h1 class="page-header__title">Editar zona</h1>

    <form method="POST" action="{{ route('publicaciones.update', $publicacion) }}" enctype="multipart/form-data" class="form">
        @csrf @method('PUT')

        <div class="form__group">
            <label class="form__label" for="titulo">Título *</label>
            <input type="text" id="titulo" name="titulo" class="form-control @error('titulo') form-control--error @enderror"
                   value="{{ old('titulo', $publicacion->titulo) }}" required maxlength="255">
            @error('titulo')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="descripcion">Descripción *</label>
            <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') form-control--error @enderror"
                      rows="5" required maxlength="2000">{{ old('descripcion', $publicacion->descripcion) }}</textarea>
            @error('descripcion')<span class="form__error">{{ $message }}</span>@enderror
        </div>

        <div class="form__group">
            <label class="form__label">Ubicación</label>
            <div id="mapa-selector"></div>
            <div class="coords-row">
                <div class="form__group">
                    <label class="form__label" for="latitud">Latitud</label>
                    <input type="number" id="latitud" name="latitud" class="form-control"
                           value="{{ old('latitud', $publicacion->latitud) }}" step="any" required readonly>
                </div>
                <div class="form__group">
                    <label class="form__label" for="longitud">Longitud</label>
                    <input type="number" id="longitud" name="longitud" class="form-control"
                           value="{{ old('longitud', $publicacion->longitud) }}" step="any" required readonly>
                </div>
            </div>
        </div>

        <div class="form__group">
            <label class="form__label">Etiquetas</label>
            <div class="checkboxes-grid">
                @foreach($etiquetas as $etiqueta)
                    <label class="checkbox-label">
                        <input type="checkbox" name="etiquetas[]" value="{{ $etiqueta->id }}"
                               {{ in_array($etiqueta->id, old('etiquetas', $etiquetasSeleccionadas)) ? 'checked' : '' }}>
                        {{ $etiqueta->nombre }}
                    </label>
                @endforeach
            </div>
        </div>

        @if($publicacion->imagenes->isNotEmpty())
            <div class="form__group">
                <label class="form__label">Imágenes actuales</label>
                <div class="galeria galeria--edit">
                    @foreach($publicacion->imagenes as $imagen)
                        <div class="galeria__item">
                            <img src="{{ asset('storage/' . $imagen->ruta) }}" class="galeria__img" alt="">
                            <form method="POST"
                                  action="{{ route('publicaciones.imagenes.destroy', [$publicacion, $imagen]) }}"
                                  class="galeria__delete">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon--danger">✕</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="form__group">
            <label class="form__label">Añadir imágenes (JPG, PNG, WEBP, GIF — máx. 5 MB cada una)</label>
            <label class="upload-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="16" height="16">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Añadir imágenes
                <input type="file" id="imagenes" name="imagenes[]"
                       accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                       multiple>
            </label>
            <span id="edit-imgs-label" style="font-size:.8rem;color:#6B7280;margin-left:.5rem"></span>
        </div>

        <div class="form__actions">
            <a href="{{ route('publicaciones.show', $publicacion) }}" class="btn btn--secondary">Cancelar</a>
            <button type="submit" class="btn btn--primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $publicacion->latitud }};
    const lng = {{ $publicacion->longitud }};

    const mapa = L.map('mapa-selector').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 18,
    }).addTo(mapa);

    let marker = L.marker([lat, lng]).addTo(mapa);

    mapa.on('click', e => {
        const { lat, lng } = e.latlng;
        document.getElementById('latitud').value = lat.toFixed(7);
        document.getElementById('longitud').value = lng.toFixed(7);
        if (marker) marker.remove();
        marker = L.marker([lat, lng]).addTo(mapa);
    });
    document.getElementById('imagenes').addEventListener('change', function () {
        const lbl = document.getElementById('edit-imgs-label');
        const files = Array.from(this.files);
        lbl.textContent = files.length ? files.length + ' archivo' + (files.length > 1 ? 's' : '') + ' seleccionado' + (files.length > 1 ? 's' : '') : '';
    });
</script>
@endpush
