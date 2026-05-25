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

        <div class="form-row-2">
            <div class="form__group">
                <label class="form__label" for="temporada">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="13" height="13" style="vertical-align:-.1em;margin-right:.25rem;opacity:.6">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Temporada
                </label>
                <select id="temporada" name="temporada"
                        class="form-control @error('temporada') form-control--error @enderror">
                    <option value="">Sin especificar</option>
                    <option value="invierno"  {{ old('temporada') === 'invierno'  ? 'selected' : '' }}>Invierno / Aguas Frías</option>
                    <option value="primavera" {{ old('temporada') === 'primavera' ? 'selected' : '' }}>Primavera / Media Estación</option>
                    <option value="verano"    {{ old('temporada') === 'verano'    ? 'selected' : '' }}>Verano / Alta Actividad</option>
                    <option value="otono"     {{ old('temporada') === 'otono'     ? 'selected' : '' }}>Otoño / Depredadores</option>
                </select>
                @error('temporada')<span class="form__error">{{ $message }}</span>@enderror
            </div>

            <div class="form__group">
                <label class="form__label" for="licencia">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="13" height="13" style="vertical-align:-.1em;margin-right:.25rem;opacity:.6">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                        <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                    </svg>
                    Licencia requerida
                </label>
                <select id="licencia" name="licencia"
                        class="form-control @error('licencia') form-control--error @enderror">
                    <option value="">Sin especificar</option>
                    <option value="interauton" {{ old('licencia') === 'interauton' ? 'selected' : '' }}>Licencia Interautonómica</option>
                    <option value="auton_1"    {{ old('licencia') === 'auton_1'    ? 'selected' : '' }}>Licencia Autonómica (1 Año)</option>
                    <option value="auton_5"    {{ old('licencia') === 'auton_5'    ? 'selected' : '' }}>Licencia Autonómica (5 Años)</option>
                    <option value="coto"       {{ old('licencia') === 'coto'       ? 'selected' : '' }}>Permiso de Coto Federado</option>
                    <option value="mar"        {{ old('licencia') === 'mar'        ? 'selected' : '' }}>Licencia de Mar / Costa</option>
                </select>
                @error('licencia')<span class="form__error">{{ $message }}</span>@enderror
            </div>
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
            <label class="form__label">Imágenes (máx. 8, hasta 5 MB cada una — JPG, PNG, WEBP, GIF)</label>
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
            <span id="imagenes-label" style="font-size:.8rem;color:#6B7280;margin-left:.5rem"></span>
            <div id="preview-imagenes" class="preview-grid" style="margin-top:.75rem"></div>
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
    document.getElementById('imagenes').addEventListener('change', function () {
        const preview = document.getElementById('preview-imagenes');
        const lbl = document.getElementById('imagenes-label');
        const files = Array.from(this.files).slice(0, 8);
        lbl.textContent = files.length ? files.length + ' archivo' + (files.length > 1 ? 's' : '') + ' seleccionado' + (files.length > 1 ? 's' : '') : '';
        preview.innerHTML = '';
        files.forEach(file => {
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
