@extends('layouts.app')

@section('title', $q ? 'Buscar: ' . $q : 'Explorar')

@section('body-class', 'app app--explore')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    /* ── Leaflet popup ── */
    .leaflet-popup-content-wrapper { border-radius: 14px; min-width: 190px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
    .leaflet-popup-content { margin: 10px 12px; }
    .popup-img { width: 100%; height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 6px; }
    .popup-titulo { font-weight: 700; font-size: .9rem; color: #1A1A1A; margin-bottom: 2px; }
    .popup-likes { font-size: .8rem; color: #6B7280; margin-bottom: 4px; }
    .popup-link { display: block; text-align: center; margin-top: 6px; padding: 5px 10px; background: #0099FF; color: #fff; border-radius: 8px; font-weight: 600; font-size: .82rem; text-decoration: none; }
    .popup-link:hover { background: #0077DD; color: #fff; }
    .marker-cluster-small div, .marker-cluster-medium div, .marker-cluster-large div { background-color: rgba(0,153,255,0.88); color: #fff; font-weight: 700; }
    .marker-cluster-small, .marker-cluster-medium, .marker-cluster-large { background-color: rgba(0,153,255,0.22); }

    /* ── Filter bar wrapper (anchors both bar and panel) ── */
    .map-filterbar-wrap {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 800;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    /* ── Pill bar ── */
    .map-filterbar {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.09);
        border-radius: 100px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    /* Search sub-section */
    .map-filterbar__search {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.45rem 0.625rem 0.45rem 0.875rem;
        color: #9CA3AF;
    }

    .map-filterbar__input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.82rem;
        font-family: inherit;
        color: #1A1A1A;
        width: 155px;
    }
    .map-filterbar__input::placeholder { color: #9CA3AF; }

    .map-filterbar__sep {
        width: 1px;
        height: 16px;
        background: rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    /* Filter toggle button */
    .map-filterbar__btn {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.45rem 0.875rem;
        border: none;
        background: transparent;
        color: #6B7280;
        font-size: 0.8rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s, color 0.15s;
        height: 100%;
    }
    .map-filterbar__btn:hover { background: rgba(0,153,255,0.07); color: #0099FF; }
    .map-filterbar__btn[aria-expanded="true"],
    .map-filterbar__btn--active { color: #0099FF; }

    .filter-chevron { transition: transform 0.18s; }
    .map-filterbar__btn[aria-expanded="true"] .filter-chevron { transform: rotate(180deg); }

    /* ── Dropdown filter panel ── */
    .map-filter-panel {
        display: none;
        margin-top: 0.5rem;
        background: rgba(255, 255, 255, 0.97);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(0, 0, 0, 0.09);
        border-radius: 16px;
        padding: 0.875rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.14);
        min-width: 240px;
        max-width: 320px;
    }
    .map-filter-panel.is-open { display: block; }

    .map-filter-panel__head {
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #6B7280;
        margin-bottom: 0.5rem;
    }

    .map-filter-panel__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
    }

    /* ── Filter chips ── */
    .map-filter-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.275rem 0.65rem;
        border: 1.5px solid rgba(0, 0, 0, 0.12);
        border-radius: 100px;
        font-size: 0.76rem;
        font-weight: 600;
        font-family: inherit;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: border-color 0.13s, background 0.13s, color 0.13s;
    }
    .map-filter-chip:hover { border-color: #0099FF; color: #0099FF; background: rgba(0,153,255,0.05); }
    .map-filter-chip--active { background: #0099FF; border-color: #0099FF; color: #fff; }
    .map-filter-chip--active:hover { background: #0077DD; border-color: #0077DD; }

    /* Season chip variant */
    .map-filter-chip--season.map-filter-chip--active { background: #059669; border-color: #059669; }
    .map-filter-chip--season.map-filter-chip--active:hover { background: #047857; border-color: #047857; }
    .map-filter-chip--season:hover { border-color: #059669; color: #059669; background: rgba(5,150,105,0.05); }

    /* License chip variant */
    .map-filter-chip--license.map-filter-chip--active { background: #6366f1; border-color: #6366f1; }
    .map-filter-chip--license.map-filter-chip--active:hover { background: #4f46e5; border-color: #4f46e5; }
    .map-filter-chip--license:hover { border-color: #6366f1; color: #6366f1; background: rgba(99,102,241,0.05); }

    /* Filter panel section separator */
    .map-filter-section + .map-filter-section { margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(0,0,0,0.06); }

    /* ── Active filter badge below bar ── */
    .map-filter-active-badge {
        display: none;
        align-items: center;
        gap: 0.375rem;
        margin-top: 0.375rem;
        padding: 0.25rem 0.625rem 0.25rem 0.5rem;
        background: #0099FF;
        color: #fff;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0,153,255,0.3);
        max-width: 300px;
    }
    .map-filter-active-badge.is-visible { display: flex; }
    .map-filter-active-badge__text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .map-filter-active-badge__clear {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 16px; height: 16px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
        border: none;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
        line-height: 1;
        padding: 0;
        flex-shrink: 0;
        transition: background 0.13s;
    }
    .map-filter-active-badge__clear:hover { background: rgba(255,255,255,0.4); }
</style>
@endpush

@section('content')
<div id="explore-mapa"></div>

{{-- ── Floating filter bar ── --}}
<div class="map-filterbar-wrap" id="map-filterbar-wrap">

    <div class="map-filterbar">
        {{-- Search --}}
        <form class="map-filterbar__search" action="{{ route('buscar') }}" method="GET">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 width="13" height="13" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="q" class="map-filterbar__input"
                   placeholder="Buscar zonas..." value="{{ $q }}" autocomplete="off">
        </form>

        <div class="map-filterbar__sep"></div>

        {{-- Filter toggle --}}
        <button class="map-filterbar__btn" id="map-filter-toggle"
                aria-expanded="false" aria-controls="map-filter-panel" type="button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25"
                 width="13" height="13" aria-hidden="true">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <span id="filter-label">Filtrar</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 width="10" height="10" class="filter-chevron" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
    </div>

    {{-- Active filter badge (shown when a species is selected) --}}
    <div class="map-filter-active-badge" id="map-filter-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             width="11" height="11" aria-hidden="true">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        <span id="badge-label"></span>
        <button class="map-filter-active-badge__clear" id="filter-clear-btn"
                type="button" title="Quitar filtro">✕</button>
    </div>

    {{-- ── Multi-filter panel ── --}}
    <div class="map-filter-panel" id="map-filter-panel" role="region" aria-label="Filtros">

        {{-- 1. Pez / Especie --}}
        <div class="map-filter-section">
            <div class="map-filter-panel__head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11" style="vertical-align:-.05em;margin-right:.25rem">
                    <path d="M6 12 C8 7 16 7 18 12 C16 17 8 17 6 12Z"/>
                    <path d="M6 12 L2 9 L2 15 Z"/>
                </svg>
                Especie
            </div>
            <div class="map-filter-panel__chips">
                <button class="map-filter-chip map-filter-chip--active"
                        data-especie="" type="button">Todas</button>
                @foreach($etiquetas as $et)
                    <button class="map-filter-chip"
                            data-especie="{{ $et->nombre }}" type="button">{{ $et->nombre }}</button>
                @endforeach
            </div>
        </div>

        {{-- 2. Temporada --}}
        <div class="map-filter-section">
            <div class="map-filter-panel__head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11" style="vertical-align:-.05em;margin-right:.25rem">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Temporada
            </div>
            <div class="map-filter-panel__chips">
                <button class="map-filter-chip map-filter-chip--season map-filter-chip--active"
                        data-temporada="" type="button">Todas</button>
                @foreach($temporadas as $key => $label)
                    <button class="map-filter-chip map-filter-chip--season"
                            data-temporada="{{ $key }}" type="button">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        {{-- 3. Licencia --}}
        <div class="map-filter-section">
            <div class="map-filter-panel__head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11" style="vertical-align:-.05em;margin-right:.25rem">
                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                </svg>
                Licencia
            </div>
            <div class="map-filter-panel__chips">
                <button class="map-filter-chip map-filter-chip--license map-filter-chip--active"
                        data-licencia="" type="button">Todas</button>
                @foreach($licencias as $key => $label)
                    <button class="map-filter-chip map-filter-chip--license"
                            data-licencia="{{ $key }}" type="button">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        {{-- Reset all --}}
        <div style="margin-top:.625rem;padding-top:.625rem;border-top:1px solid rgba(0,0,0,0.06)">
            <button id="filter-reset-all" type="button"
                    style="width:100%;padding:.3rem;border:none;background:transparent;color:#9CA3AF;font-size:.74rem;font-weight:600;font-family:inherit;cursor:pointer;border-radius:6px;transition:background .13s,color .13s"
                    onmouseover="this.style.background='rgba(0,0,0,0.04)';this.style.color='#374151'"
                    onmouseout="this.style.background='transparent';this.style.color='#9CA3AF'">
                Limpiar todos los filtros
            </button>
        </div>
    </div>

</div>

@if($q)
<div class="explore-search-results">
    <span class="explore-search-results__count">
        {{ $publicaciones->count() }} resultado{{ $publicaciones->count() !== 1 ? 's' : '' }} para
        <strong>"{{ $q }}"</strong>
    </span>
    <a href="{{ route('buscar') }}" class="explore-search-results__clear">✕ Limpiar</a>
</div>
@endif
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
(function () {
    var spainBounds = L.latLngBounds([27.4, -19.0], [44.2, 5.0]);
    var mapa = L.map('explore-mapa', {
        zoomControl: true,
        maxBounds: spainBounds,
        maxBoundsViscosity: 0.85,
        minZoom: 5,
    }).setView([40.4, -3.7], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18,
    }).addTo(mapa);

    var icono = L.divIcon({
        html: '<div class="marker-pin"></div>',
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 28],
        popupAnchor: [0, -30],
    });

    @php
    $mapData = $publicaciones->map(function ($p) {
        $imagen = $p->imagenes->first();
        return [
            'titulo'    => $p->titulo,
            'lat'       => (float) $p->latitud,
            'lng'       => (float) $p->longitud,
            'url'       => route('publicaciones.show', $p),
            'likes'     => $p->likes->count(),
            'imagen'    => $imagen ? asset('storage/' . $imagen->ruta) : null,
            'etiquetas' => $p->etiquetas->pluck('nombre')->values()->toArray(),
            'temporada' => $p->temporada ?? '',
            'licencia'  => $p->licencia  ?? '',
        ];
    })->values();
    @endphp

    var zonas = @json($mapData);

    /* ── Marker store ── */
    var allMarkers = [];

    function buildCapa() {
        return (typeof L.markerClusterGroup === 'function')
            ? L.markerClusterGroup({ maxClusterRadius: 60, showCoverageOnHover: false })
            : L.layerGroup();
    }

    var capaActiva = buildCapa();

    zonas.forEach(function (zona) {
        if (!zona.lat || !zona.lng) return;
        var marker = L.marker([zona.lat, zona.lng], { icon: icono });
        var imgHtml = zona.imagen
            ? '<img src="' + zona.imagen + '" class="popup-img" alt="">'
            : '';
        marker.bindPopup(
            imgHtml
            + '<div class="popup-titulo">' + zona.titulo + '</div>'
            + '<div class="popup-likes">♥ ' + zona.likes + ' me gusta</div>'
            + '<a href="' + zona.url + '" class="popup-link">Ver zona →</a>',
            { maxWidth: 210 }
        );
        marker.on('click', function () {
            mapa.flyTo([zona.lat, zona.lng], 14, { animate: true, duration: 0.8 });
        });
        allMarkers.push({ marker: marker, zona: zona });
        capaActiva.addLayer(marker);
    });

    mapa.addLayer(capaActiva);

    /* ── Active filter state ── */
    var activeEspecie   = '';
    var activeTemporada = '';
    var activeLicencia  = '';

    /* ── Re-render markers matching ALL active filters (AND logic) ── */
    function applyFilters() {
        mapa.removeLayer(capaActiva);
        capaActiva = buildCapa();
        allMarkers.forEach(function (item) {
            var z = item.zona;
            var matchEsp  = !activeEspecie   || z.etiquetas.indexOf(activeEspecie)   >= 0;
            var matchTemp = !activeTemporada || z.temporada === activeTemporada;
            var matchLic  = !activeLicencia  || z.licencia  === activeLicencia;
            if (matchEsp && matchTemp && matchLic) capaActiva.addLayer(item.marker);
        });
        mapa.addLayer(capaActiva);
        updateBadge();
    }

    /* ── Build summary badge text ── */
    var filterToggle = document.getElementById('map-filter-toggle');
    var filterPanel  = document.getElementById('map-filter-panel');
    var filterLabel  = document.getElementById('filter-label');
    var filterBadge  = document.getElementById('map-filter-badge');
    var badgeLabel   = document.getElementById('badge-label');
    var filterWrap   = document.getElementById('map-filterbar-wrap');

    function updateBadge() {
        var parts = [];
        if (activeEspecie)   parts.push(activeEspecie);
        if (activeTemporada) parts.push(activeTemporada);
        if (activeLicencia)  parts.push(activeLicencia);
        var hasFilter = parts.length > 0;
        filterToggle.classList.toggle('map-filterbar__btn--active', hasFilter);
        if (hasFilter) {
            badgeLabel.textContent = parts.join(' · ');
            filterBadge.classList.add('is-visible');
        } else {
            filterBadge.classList.remove('is-visible');
        }
    }

    /* ── Chip click handlers ── */
    document.querySelectorAll('[data-especie]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            activeEspecie = this.dataset.especie;
            document.querySelectorAll('[data-especie]').forEach(function (c) {
                c.classList.toggle('map-filter-chip--active', c.dataset.especie === activeEspecie);
            });
            applyFilters();
        });
    });

    document.querySelectorAll('[data-temporada]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            activeTemporada = this.dataset.temporada;
            document.querySelectorAll('[data-temporada]').forEach(function (c) {
                c.classList.toggle('map-filter-chip--active', c.dataset.temporada === activeTemporada);
            });
            applyFilters();
        });
    });

    document.querySelectorAll('[data-licencia]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            activeLicencia = this.dataset.licencia;
            document.querySelectorAll('[data-licencia]').forEach(function (c) {
                c.classList.toggle('map-filter-chip--active', c.dataset.licencia === activeLicencia);
            });
            applyFilters();
        });
    });

    /* ── Reset all filters ── */
    function resetAllFilters() {
        activeEspecie = activeTemporada = activeLicencia = '';
        document.querySelectorAll('[data-especie]').forEach(function (c) {
            c.classList.toggle('map-filter-chip--active', c.dataset.especie === '');
        });
        document.querySelectorAll('[data-temporada]').forEach(function (c) {
            c.classList.toggle('map-filter-chip--active', c.dataset.temporada === '');
        });
        document.querySelectorAll('[data-licencia]').forEach(function (c) {
            c.classList.toggle('map-filter-chip--active', c.dataset.licencia === '');
        });
        applyFilters();
    }

    document.getElementById('filter-reset-all').addEventListener('click', resetAllFilters);
    document.getElementById('filter-clear-btn').addEventListener('click', function () {
        resetAllFilters();
        filterPanel.classList.remove('is-open');
        filterToggle.setAttribute('aria-expanded', 'false');
    });

    /* Toggle panel open/close */
    filterToggle.addEventListener('click', function () {
        var open = filterPanel.classList.toggle('is-open');
        filterToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    /* Close panel on outside click */
    document.addEventListener('click', function (e) {
        if (filterWrap && !filterWrap.contains(e.target)) {
            filterPanel.classList.remove('is-open');
            filterToggle.setAttribute('aria-expanded', 'false');
        }
    });

}());
</script>
@endpush
