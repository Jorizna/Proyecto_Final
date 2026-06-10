@extends('layouts.app')

@section('title', $publicacion->titulo)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="post-wrapper">
    <article class="post">
        <header class="post__header">
            <a href="{{ route('usuarios.show', $publicacion->user) }}" class="post__avatar-link">
                @if($publicacion->user->avatar)
                    <img src="{{ asset('storage/' . $publicacion->user->avatar) }}"
                         alt="{{ $publicacion->user->name }}" class="avatar avatar--md">
                @else
                    <div class="avatar avatar--md avatar--placeholder">
                        {{ strtoupper(substr($publicacion->user->name, 0, 1)) }}
                    </div>
                @endif
            </a>

            <div class="post__meta">
                <a href="{{ route('usuarios.show', $publicacion->user) }}" class="post__author">
                    {{ $publicacion->user->name }}
                </a>
                <span class="post__fecha">{{ $publicacion->created_at->diffForHumans() }}</span>
                @if($publicacion->etiquetas->isNotEmpty())
                    <div class="tags post__tags">
                        @foreach($publicacion->etiquetas as $et)
                            <span class="tag">{{ $et->nombre }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            @canany(['update', 'delete'], $publicacion)
                <div class="post-actions">
                    <button class="post-actions__trigger" data-actions-trigger aria-label="Opciones">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                            <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                        </svg>
                    </button>
                    <div class="post-actions__dropdown">
                        @can('update', $publicacion)
                            <a href="{{ route('publicaciones.edit', $publicacion) }}" class="post-actions__item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="15" height="15">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Editar zona
                            </a>
                        @endcan
                        @can('delete', $publicacion)
                            <form method="POST" action="{{ route('publicaciones.destroy', $publicacion) }}"
                                  id="delete-pub-{{ $publicacion->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="post-actions__item post-actions__item--danger"
                                        onclick="if(confirm('¿Eliminar esta zona?')) document.getElementById('delete-pub-{{ $publicacion->id }}').submit()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="15" height="15">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                    Eliminar zona
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endcanany
        </header>
        <div class="post__body">
            <h1 class="post__titulo">{{ $publicacion->titulo }}</h1>
            @if($publicacion->temporada || $publicacion->licencia)
                <div class="post-meta-strip">
                    @if($publicacion->temporada)
                        <div class="post-meta-item post-meta-item--season">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="13" height="13">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span class="post-meta-item__label">Temporada</span>
                            <span class="post-meta-item__value">{{ $publicacion->temporadaLabel() }}</span>
                        </div>
                    @endif
                    @if($publicacion->licencia)
                        <div class="post-meta-item post-meta-item--license">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="13" height="13">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                <line x1="12" y1="12" x2="12" y2="16"/>
                                <line x1="10" y1="14" x2="14" y2="14"/>
                            </svg>
                            <span class="post-meta-item__label">Licencia</span>
                            <span class="post-meta-item__value">{{ $publicacion->licenciaLabel() }}</span>
                        </div>
                    @endif
                </div>
            @endif

            @if($publicacion->descripcion)
                <p class="post__descripcion">{{ $publicacion->descripcion }}</p>
            @endif
        </div>
        @if($publicacion->latitud && $publicacion->longitud)
            <div class="post-map-widget">
                <div id="post-mini-map"></div>
            </div>
        @endif
        @if($publicacion->imagenes->isNotEmpty())
            @php $imgs = $publicacion->imagenes; $n = $imgs->count(); @endphp
            @if($n === 1)
                <div class="carousel carousel--single">
                    <div class="carousel__track" data-carousel-track>
                        <div class="carousel__slide">
                            <img src="{{ asset('storage/' . $imgs->first()->ruta) }}"
                                 alt="{{ $publicacion->titulo }}"
                                 data-lightbox-src="{{ asset('storage/' . $imgs->first()->ruta) }}"
                                 title="Ver imagen completa">
                        </div>
                    </div>
                    @can('update', $publicacion)
                        <form method="POST"
                              action="{{ route('publicaciones.imagenes.destroy', [$publicacion, $imgs->first()]) }}"
                              class="fotos-grid__delete" style="position:absolute;top:.5rem;right:.5rem;z-index:20">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-icon" title="Eliminar imagen"
                                    onclick="if(confirm('¿Eliminar esta imagen?')) this.closest('form').submit()">✕</button>
                        </form>
                    @endcan
                </div>
            @else
                <div class="carousel" id="post-carousel">
                    <div class="carousel__track" data-carousel-track>
                        @foreach($imgs as $img)
                            <div class="carousel__slide">
                                <img src="{{ asset('storage/' . $img->ruta) }}"
                                     alt="{{ $publicacion->titulo }}"
                                     data-lightbox-src="{{ asset('storage/' . $img->ruta) }}"
                                     title="Ver imagen completa">
                                @can('update', $publicacion)
                                    <form method="POST"
                                          action="{{ route('publicaciones.imagenes.destroy', [$publicacion, $img]) }}"
                                          style="position:absolute;top:.5rem;right:.5rem;z-index:20">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-icon" title="Eliminar"
                                                onclick="if(confirm('¿Eliminar esta imagen?')) this.closest('form').submit()">✕</button>
                                    </form>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel__btn carousel__btn--prev" aria-label="Anterior">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                    <button class="carousel__btn carousel__btn--next" aria-label="Siguiente">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                    <div class="carousel__dots">
                        @foreach($imgs as $i => $img)
                            <button class="carousel__dot {{ $i === 0 ? 'carousel__dot--active' : '' }}"
                                    aria-label="Imagen {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
        <div class="interactions">
            <form method="POST" action="{{ route('likes.toggle', $publicacion) }}">
                @csrf
                <button type="submit"
                        class="int-btn int-btn--like {{ $esLiked ? 'int-btn--active' : '' }}"
                        title="{{ $esLiked ? 'Quitar me gusta' : 'Me gusta' }}">
                    <svg viewBox="0 0 24 24" fill="{{ $esLiked ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span>{{ $publicacion->likes->count() }}</span>
                </button>
            </form>

            <form method="POST" action="{{ route('repostes.toggle', $publicacion) }}">
                @csrf
                <button type="submit"
                        class="int-btn int-btn--repost {{ $esReposteado ? 'int-btn--active' : '' }}"
                        title="{{ $esReposteado ? 'Quitar repost' : 'Repostear' }}">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="17 1 21 5 17 9"/>
                        <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                        <polyline points="7 23 3 19 7 15"/>
                        <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                    </svg>
                    <span>{{ $publicacion->repostes->count() }}</span>
                </button>
            </form>

            <div class="interactions__spacer"></div>

            <form method="POST" action="{{ route('favoritos.toggle', $publicacion) }}">
                @csrf
                <button type="submit"
                        class="int-btn int-btn--bookmark {{ $esFavorito ? 'int-btn--active' : '' }}"
                        title="{{ $esFavorito ? 'Guardado' : 'Guardar' }}">
                    <svg viewBox="0 0 24 24" fill="{{ $esFavorito ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>{{ $publicacion->favoritos->count() }}</span>
                </button>
            </form>
        </div>
        <div class="replies" id="respuestas">
            <div class="replies__header">Respuestas</div>

            @auth
                <form method="POST" action="{{ route('comentarios.store', $publicacion) }}"
                      class="reply-composer" enctype="multipart/form-data">
                    @csrf
                    <div class="reply-composer__avatar">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name }}" class="avatar avatar--sm">
                        @else
                            <div class="avatar avatar--sm avatar--placeholder">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="reply-composer__content">
                        <textarea name="texto" class="reply-composer__textarea"
                                  placeholder="Escribe tu respuesta..." maxlength="1000"
                                  rows="2"></textarea>
                        <div id="img-preview" class="reply-preview"></div>
                        <div class="reply-composer__toolbar">
                            <label class="upload-btn" title="Adjuntar imágenes (máx. 4)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="16" height="16">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                Añadir imagen
                                <input type="file" name="imagenes[]" accept="image/*" multiple
                                       id="reply-imgs" max="4">
                            </label>
                            <span id="reply-imgs-label" class="reply-imgs-label"></span>
                            <button type="submit" class="btn btn--primary btn--sm">Responder</button>
                        </div>
                    </div>
                </form>
            @else
                <p class="replies__login">
                    <a href="{{ route('login') }}">Inicia sesión</a> para responder.
                </p>
            @endauth
            @forelse($publicacion->comentarios as $comentario)
                @include('publicaciones._reply', [
                    'comment'     => $comentario,
                    'depth'       => 0,
                    'publicacion' => $publicacion,
                ])
            @empty
                <p class="replies__empty">Sin respuestas todavía. ¡Sé el primero!</p>
            @endforelse
        </div>

    </article>
</div>

<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Visor de imágenes">
    <div class="lightbox__backdrop" id="lightbox-backdrop"></div>
    <button class="lightbox__close" id="lightbox-close" aria-label="Cerrar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>
    <div class="lightbox__img-wrap">
        <button class="lightbox__nav lightbox__nav--prev" id="lightbox-prev" aria-label="Imagen anterior">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </button>
        <img class="lightbox__img" id="lightbox-img" src="" alt="">
        <button class="lightbox__nav lightbox__nav--next" id="lightbox-next" aria-label="Imagen siguiente">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </button>
        <div class="lightbox__counter" id="lightbox-counter"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
/* Mini-map */
(function () {
    var el = document.getElementById('post-mini-map');
    if (!el) return;
    var lat = {{ $publicacion->latitud ?? 'null' }};
    var lng = {{ $publicacion->longitud ?? 'null' }};
    if (!lat || !lng) return;

    var mapa = L.map('post-mini-map', { zoomControl: true, scrollWheelZoom: false })
                .setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap', maxZoom: 18
    }).addTo(mapa);

    var icono = L.divIcon({
        html: '<div class="marker-pin"></div>',
        className: '', iconSize: [28,28], iconAnchor: [14,28], popupAnchor: [0,-30]
    });

    L.marker([lat, lng], { icon: icono })
     .addTo(mapa)
     .bindPopup('<strong>{{ addslashes($publicacion->titulo) }}</strong>')
     .openPopup();
}());

/* Carousel */
(function () {
    var carousel = document.getElementById('post-carousel');
    if (!carousel) return;
    var track   = carousel.querySelector('[data-carousel-track]');
    var slides  = carousel.querySelectorAll('.carousel__slide');
    var btnPrev = carousel.querySelector('.carousel__btn--prev');
    var btnNext = carousel.querySelector('.carousel__btn--next');
    var dots    = carousel.querySelectorAll('.carousel__dot');
    var current = 0;
    var total   = slides.length;

    function goTo(n) {
        current = Math.max(0, Math.min(n, total - 1));
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        if (btnPrev) btnPrev.disabled = current === 0;
        if (btnNext) btnNext.disabled = current === total - 1;
        dots.forEach(function (d, i) {
            d.classList.toggle('carousel__dot--active', i === current);
        });
    }

    if (btnPrev) btnPrev.addEventListener('click', function () { goTo(current - 1); });
    if (btnNext) btnNext.addEventListener('click', function () { goTo(current + 1); });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { goTo(i); }); });

    var startX = 0;
    carousel.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    carousel.addEventListener('touchend', function (e) {
        var diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) goTo(diff > 0 ? current + 1 : current - 1);
    });

    goTo(0);
}());

/* Image preview for main reply composer */
(function () {
    var input   = document.getElementById('reply-imgs');
    var label   = document.getElementById('reply-imgs-label');
    var preview = document.getElementById('img-preview');
    if (!input) return;

    input.addEventListener('change', function () {
        var files = Array.from(this.files).slice(0, 4);
        label.textContent = files.length ? files.length + ' imagen' + (files.length > 1 ? 'es' : '') : '';
        preview.innerHTML = '';
        files.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'reply-preview__thumb';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
}());

/* Toggle inline reply composer */
function toggleChildComposer(commentId) {
    var el = document.getElementById('child-composer-' + commentId);
    if (!el) return;
    el.classList.toggle('is-open');
    if (el.classList.contains('is-open')) el.querySelector('textarea').focus();
}

/* Lightbox */
(function () {
    var images   = Array.from(document.querySelectorAll('[data-lightbox-src]'));
    if (!images.length) return;

    var lb       = document.getElementById('lightbox');
    var lbImg    = document.getElementById('lightbox-img');
    var lbPrev   = document.getElementById('lightbox-prev');
    var lbNext   = document.getElementById('lightbox-next');
    var lbCounter = document.getElementById('lightbox-counter');
    var current  = 0;

    function update() {
        lbImg.src = images[current].dataset.lightboxSrc;
        lbImg.alt = images[current].alt || '';
        var multi = images.length > 1;
        lbPrev.style.display = multi ? '' : 'none';
        lbNext.style.display = multi ? '' : 'none';
        lbCounter.textContent = multi ? (current + 1) + ' / ' + images.length : '';
        lbPrev.disabled = current === 0;
        lbNext.disabled = current === images.length - 1;
    }

    function open(i)  { current = i; lb.classList.add('is-open'); document.body.style.overflow = 'hidden'; update(); }
    function close()  { lb.classList.remove('is-open'); document.body.style.overflow = ''; }

    images.forEach(function (el, i) { el.addEventListener('click', function () { open(i); }); });
    document.getElementById('lightbox-close').addEventListener('click', close);
    document.getElementById('lightbox-backdrop').addEventListener('click', close);
    lbPrev.addEventListener('click', function () { if (current > 0) { current--; update(); } });
    lbNext.addEventListener('click', function () { if (current < images.length - 1) { current++; update(); } });

    document.addEventListener('keydown', function (e) {
        if (!lb.classList.contains('is-open')) return;
        if (e.key === 'Escape')    close();
        if (e.key === 'ArrowLeft'  && current > 0)                 { current--; update(); }
        if (e.key === 'ArrowRight' && current < images.length - 1) { current++; update(); }
    });
}());

/* Toggle nested child replies — independent per comment, recursive-safe */
function toggleReplies(id, btn) {
    var replies = document.getElementById('replies-' + id);
    if (!replies) return;
    var opening = !replies.classList.contains('is-open');
    replies.classList.toggle('is-open');
    var count = btn.dataset.count;
    btn.innerHTML = opening
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="18 15 12 9 6 15"/></svg> Ocultar respuestas'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="6 9 12 15 18 9"/></svg> Ver respuestas (' + count + ')';
}
</script>
@endpush
