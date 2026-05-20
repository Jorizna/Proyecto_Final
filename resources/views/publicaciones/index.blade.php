@extends('layouts.app')

@section('title', 'Mapa de zonas de pesca')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    .mapa-wrapper {
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    #mapa { height: 520px; width: 100%; }
    .leaflet-popup-content-wrapper { border-radius: 10px; min-width: 200px; }
    .leaflet-popup-content { margin: 10px 12px; }
    .popup-img { width: 100%; height: 110px; object-fit: cover; border-radius: 6px; margin-bottom: 6px; }
    .popup-titulo { font-weight: 700; font-size: .95rem; color: #1a4731; margin-bottom: 2px; }
    .popup-likes { font-size: .82rem; color: #6b7280; margin-bottom: 4px; }
    .popup-etiquetas { margin-bottom: 6px; display: flex; flex-wrap: wrap; gap: 3px; }
    .popup-link {
        display: block;
        text-align: center;
        margin-top: 6px;
        padding: 5px 10px;
        background: #2e7d52;
        color: #fff;
        border-radius: 6px;
        font-weight: 600;
        font-size: .85rem;
        text-decoration: none;
    }
    .popup-link:hover { background: #1a4731; color: #fff; }
    .marker-cluster-small div,
    .marker-cluster-medium div,
    .marker-cluster-large div {
        background-color: rgba(46,125,82,.85);
        color: #fff;
        font-weight: 700;
    }
    .marker-cluster-small,
    .marker-cluster-medium,
    .marker-cluster-large { background-color: rgba(46,125,82,.25); }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-header__title">Zonas de pesca en Aragón</h1>
    <p class="page-header__subtitle">{{ $publicaciones->count() }} zonas publicadas por la comunidad</p>
</div>

<div class="mapa-wrapper">
    <div id="mapa"></div>
</div>

<section class="section">
    <h2 class="section__title">Últimas zonas publicadas</h2>

    @if($publicaciones->isEmpty())
        <div class="empty-state">
            <p>Todavía no hay zonas publicadas.</p>
            @auth
                <a href="{{ route('publicaciones.create') }}" class="btn btn--primary">Sé el primero en publicar</a>
            @endauth
        </div>
    @else
        <div class="cards-grid">
            @foreach($publicaciones->take(12) as $pub)
                @php $primeraImagen = $pub->imagenes->first(); @endphp
                <article class="card">
                    @if($primeraImagen)
                        <img src="{{ asset('storage/' . $primeraImagen->ruta) }}"
                             alt="{{ $pub->titulo }}"
                             class="card__img">
                    @else
                        <div class="card__img card__img--placeholder">Sin imagen</div>
                    @endif

                    <div class="card__body">
                        <h3 class="card__title">
                            <a href="{{ route('publicaciones.show', $pub) }}">{{ $pub->titulo }}</a>
                        </h3>
                        <div class="card__meta">
                            <span class="card__likes">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                                {{ $pub->likes->count() }}
                            </span>
                            <span class="card__author">por {{ $pub->user->name }}</span>
                        </div>
                        @if($pub->etiquetas->isNotEmpty())
                            <div class="tags">
                                @foreach($pub->etiquetas as $etiqueta)
                                    <span class="tag">{{ $etiqueta->nombre }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
(function () {
    var mapa = L.map('mapa', { zoomControl: true }).setView([41.6, -0.9], 8);

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

    var capa = (typeof L.markerClusterGroup === 'function')
        ? L.markerClusterGroup({ maxClusterRadius: 60, showCoverageOnHover: false })
        : mapa;

    @php
    $mapData = $publicaciones->map(function ($p) {
        $imagen = $p->imagenes->first();
        return [
            'id'       => $p->id,
            'titulo'   => $p->titulo,
            'lat'      => (float) $p->latitud,
            'lng'      => (float) $p->longitud,
            'url'      => route('publicaciones.show', $p),
            'likes'    => $p->likes->count(),
            'imagen'   => $imagen ? asset('storage/' . $imagen->ruta) : null,
            'etiquetas'=> $p->etiquetas->pluck('nombre')->values(),
        ];
    })->values();
    @endphp

    var zonas = @json($mapData);

    zonas.forEach(function (zona) {
        if (!zona.lat || !zona.lng) return;

        var marker = L.marker([zona.lat, zona.lng], { icon: icono });

        var imgHtml = zona.imagen
            ? '<img src="' + zona.imagen + '" class="popup-img" alt="">'
            : '';

        var etiquetasHtml = '';
        if (zona.etiquetas && zona.etiquetas.length) {
            etiquetasHtml = '<div class="popup-etiquetas">';
            zona.etiquetas.forEach(function (e) {
                etiquetasHtml += '<span class="tag">' + e + '</span>';
            });
            etiquetasHtml += '</div>';
        }

        marker.bindPopup(
            imgHtml
            + '<div class="popup-titulo">' + zona.titulo + '</div>'
            + '<div class="popup-likes">' + zona.likes + ' likes</div>'
            + etiquetasHtml
            + '<a href="' + zona.url + '" class="popup-link">Ver detalle</a>',
            { maxWidth: 220 }
        );

        marker.on('click', function () {
            mapa.flyTo([zona.lat, zona.lng], 14, { animate: true, duration: 0.8 });
        });

        if (capa === mapa) {
            marker.addTo(mapa);
        } else {
            capa.addLayer(marker);
        }
    });

    if (capa !== mapa) {
        mapa.addLayer(capa);
    }
}());
</script>
@endpush
