@extends('layouts.app')

@section('title', 'Equipamiento y Guías')

@section('content')
<div class="hub-wrapper">

    {{-- ── PAGE HEADER ── --}}
    <div class="hub-header">
        <div class="hub-header__left">
            <div class="feed-header__eyebrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    <line x1="10" y1="7" x2="16" y2="7"/><line x1="10" y1="11" x2="16" y2="11"/>
                </svg>
                Biblioteca de pesca
            </div>
            <h1 class="feed-header__title" style="font-size:clamp(1.75rem,3vw,2.5rem)">Equipamiento<br>y Guías</h1>
            <p class="feed-header__sub">Técnicas, material y entornos — todo lo que necesitas para pescar mejor.</p>
        </div>
        <a href="{{ route('tutoriales.create') }}" class="btn btn--primary hub-header__cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" width="15" height="15">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Añadir tutorial
        </a>
    </div>

    {{-- ════════════════════════════════
         SECCIÓN 1 — TÉCNICAS MAESTRAS
         Grid de 3 tarjetas compactas
         ════════════════════════════════ --}}
    <section class="hub-section">
        <div class="hub-section__head hub-section__head--blue">
            <div class="hub-section__eyebrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                Técnicas Maestras
            </div>
            <h2 class="hub-section__title">Métodos y Estilos</h2>
            <p class="hub-section__sub">Guías de técnica por modalidad: desde spinning hasta carpfishing.</p>
        </div>

        <div class="hub-tech-grid">

            {{-- CARD: Spinning con Señuelo --}}
            <div class="hub-tech-card" id="tech-spinning">
                <div class="hub-tech-card__top hub-tech-card__top--brand">
                    <div class="hub-tech-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <line x1="4" y1="20" x2="19" y2="5" stroke-linecap="round"/>
                            <circle cx="20.5" cy="3.5" r="1.75"/>
                        </svg>
                    </div>
                    <span class="hub-tech-badge">Agua dulce</span>
                </div>
                <div class="hub-tech-card__body">
                    <h3 class="hub-tech-card__title">Spinning con Señuelo</h3>
                    <p class="hub-tech-card__desc">Crankbaits, vinils y jigs para Black Bass y Lucio en embalses y lagos. Técnicas de recuperación continua, stop-and-go y jigging vertical.</p>
                    <div class="hub-tech-tags">
                        <span class="hub-tech-tag">Black Bass</span>
                        <span class="hub-tech-tag">Lucio</span>
                        <span class="hub-tech-tag">Jigging</span>
                    </div>
                </div>
                <button class="hub-tech-card__toggle" onclick="toggleTech('tech-spinning')" aria-expanded="false">
                    <span class="hub-tech-card__toggle-text">Ver guía</span>
                    <svg class="hub-tech-card__toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="hub-tech-card__drawer">
                    <div class="guia-content">
                        <div class="guia-section">
                            <div class="guia-section__title">Señuelos Clave</div>
                            <div class="guia-table-wrap">
                                <table class="guia-table">
                                    <thead><tr><th>Señuelo</th><th>Profundidad</th><th>Técnica</th></tr></thead>
                                    <tbody>
                                        <tr><td>Crankbait flotante</td><td>0,5–3 m</td><td>Stop-and-Go, continua</td></tr>
                                        <tr><td>Vinil / Grub</td><td>2–10 m</td><td>Texas Rig, Jigging</td></tr>
                                        <tr><td>Popper topwater</td><td>Superficie</td><td>Splash-and-pause</td></tr>
                                        <tr><td>Swimbait articulado</td><td>1–5 m</td><td>Imitación natural lenta</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Montaje Recomendado</div>
                            <div class="guia-chips">
                                <span class="guia-chip">Caña spinning 2,1–2,4 m / ML-M</span>
                                <span class="guia-chip">Carrete 2500–3000</span>
                                <span class="guia-chip">Trenzado PE 0,12–0,17</span>
                                <span class="guia-chip">Bajo FC 0,22–0,28</span>
                            </div>
                        </div>
                        <div class="guia-tip"><strong>Clave:</strong> Los mejores momentos son alba y ocaso. Busca estructuras: puntas de roca, cañaverales y cambios bruscos de profundidad. La termoclina concentra depredadores entre 4–8 m en verano.</div>
                    </div>
                </div>
            </div>

            {{-- CARD: Surfcasting --}}
            <div class="hub-tech-card" id="tech-surf">
                <div class="hub-tech-card__top hub-tech-card__top--purple">
                    <div class="hub-tech-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <path d="M2 9 Q5.5 6 9 9 Q12.5 12 16 9 Q19.5 6 22 9" stroke-linecap="round" fill="none"/>
                            <path d="M2 16 Q5.5 13 9 16 Q12.5 19 16 16 Q19.5 13 22 16" stroke-linecap="round" fill="none"/>
                        </svg>
                    </div>
                    <span class="hub-tech-badge hub-tech-badge--purple">Costa</span>
                </div>
                <div class="hub-tech-card__body">
                    <h3 class="hub-tech-card__title">Surfcasting</h3>
                    <p class="hub-tech-card__desc">Lanzamientos de 60–150 m desde playa para Dorada, Lubina y Corvina. Terminal con dos ramales y plomo torpedo. Buen cebo: calamar y Arenicola.</p>
                    <div class="hub-tech-tags">
                        <span class="hub-tech-tag">Dorada</span>
                        <span class="hub-tech-tag">Lubina</span>
                        <span class="hub-tech-tag">Playa</span>
                    </div>
                </div>
                <button class="hub-tech-card__toggle" onclick="toggleTech('tech-surf')" aria-expanded="false">
                    <span class="hub-tech-card__toggle-text">Ver guía</span>
                    <svg class="hub-tech-card__toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="hub-tech-card__drawer">
                    <div class="guia-content">
                        <div class="guia-section">
                            <div class="guia-section__title">Montaje Surfcasting</div>
                            <p>Plomo torpedo o bomba de 100–200 g. Dos ramales de fluorocarbono 0,50–0,60 mm con anzuelos 1/0 a 4/0. Baja de línea monofilamento reforzada 40–60 lb. Trenzado 0,22–0,28 mm en carrete de gran capacidad.</p>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Material</div>
                            <div class="guia-chips">
                                <span class="guia-chip">Caña surf 4,2–4,5 m / H-XH</span>
                                <span class="guia-chip">Carrete 6000–8000</span>
                                <span class="guia-chip">Trenzado 0,22–0,30</span>
                                <span class="guia-chip">Bajo mono 40–60 lb</span>
                            </div>
                        </div>
                        <div class="guia-tip"><strong>Mediterráneo:</strong> Las doradas se acercan a orilla en otoño tras las primeras lluvias y temporal de levante. Pesca de noche y al amanecer con la marejada aún presente para mejores resultados.</div>
                    </div>
                </div>
            </div>

            {{-- CARD: Carpfishing --}}
            <div class="hub-tech-card" id="tech-carp">
                <div class="hub-tech-card__top hub-tech-card__top--green">
                    <div class="hub-tech-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <path d="M6 12 C8 7 16 7 18 12 C16 17 8 17 6 12Z" stroke-linejoin="round"/>
                            <path d="M6 12 L2 9 L2 15 Z" stroke-linejoin="round"/>
                            <circle cx="16" cy="11" r="1" fill="currentColor" stroke="none"/>
                        </svg>
                    </div>
                    <span class="hub-tech-badge hub-tech-badge--green">Agua dulce</span>
                </div>
                <div class="hub-tech-card__body">
                    <h3 class="hub-tech-card__title">Carpfishing</h3>
                    <p class="hub-tech-card__desc">Bolt rig, hair rig y método feeder para carpas de gran tamaño en embalses. Boilies, maíz y pellets como cebo. Cebado estratégico del punto.</p>
                    <div class="hub-tech-tags">
                        <span class="hub-tech-tag">Carpa</span>
                        <span class="hub-tech-tag">Bolt Rig</span>
                        <span class="hub-tech-tag">Boilies</span>
                    </div>
                </div>
                <button class="hub-tech-card__toggle" onclick="toggleTech('tech-carp')" aria-expanded="false">
                    <span class="hub-tech-card__toggle-text">Ver guía</span>
                    <svg class="hub-tech-card__toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="hub-tech-card__drawer">
                    <div class="guia-content">
                        <div class="guia-section">
                            <div class="guia-section__title">Rigs Fundamentales</div>
                            <div class="guia-table-wrap">
                                <table class="guia-table">
                                    <thead><tr><th>Rig</th><th>Cuándo usar</th></tr></thead>
                                    <tbody>
                                        <tr><td>Bolt Rig</td><td>Fondo duro, pesca larga distancia</td></tr>
                                        <tr><td>Hair Rig</td><td>Boilies, cebo separado del anzuelo</td></tr>
                                        <tr><td>Method Feeder</td><td>Fondo blando, distancias medias</td></tr>
                                        <tr><td>Chod Rig</td><td>Fondos con algas o barro</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Material</div>
                            <div class="guia-chips">
                                <span class="guia-chip">Caña carpa 3,6–3,9 m / 3 lb</span>
                                <span class="guia-chip">Carrete big pit 10000+</span>
                                <span class="guia-chip">Trenzado 0,28–0,35</span>
                                <span class="guia-chip">Líderes shock 50 lb</span>
                            </div>
                        </div>
                        <div class="guia-tip"><strong>Estrategia:</strong> Ceba el punto durante 2–3 días antes con boilies y PVA bags de pellets. Coloca los montajes sobre el cebado. Las carpas grandes se mueven de noche — madrugada es el mejor momento.</div>
                    </div>
                </div>
            </div>

        </div>{{-- .hub-tech-grid --}}
    </section>

    {{-- ════════════════════════════════
         SECCIÓN 2 — EQUIPAMIENTO
         Listado desplegable detallado
         ════════════════════════════════ --}}
    <section class="hub-section">
        <div class="hub-section__head hub-section__head--amber">
            <div class="hub-section__eyebrow hub-section__eyebrow--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Material y Gear
            </div>
            <h2 class="hub-section__title">Equipamiento y Señuelos</h2>
            <p class="hub-section__sub">Análisis de cañas, carretes y señuelos según el tipo de agua y especie objetivo.</p>
        </div>

        <div class="guias-grid">

            <div class="guia-card" id="eq-canas">
                <button class="guia-card__header" onclick="toggleGuia('eq-canas')" aria-expanded="false">
                    <div class="guia-card__icon guia-card__icon--amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <line x1="3" y1="21" x2="21" y2="3" stroke-linecap="round"/>
                            <circle cx="8" cy="16" r="1.5"/>
                            <circle cx="14" cy="10" r="1.5"/>
                        </svg>
                    </div>
                    <div class="guia-card__meta">
                        <span class="guia-badge guia-badge--amber">Material</span>
                        <h3 class="guia-card__title">Cañas de Pesca</h3>
                        <p class="guia-card__subtitle">Spinning · Surfcasting · Float · Carpfishing</p>
                    </div>
                    <svg class="guia-card__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="guia-card__body">
                    <div class="guia-content">
                        <div class="guia-section">
                            <div class="guia-table-wrap">
                                <table class="guia-table">
                                    <thead><tr><th>Tipo</th><th>Longitud</th><th>Potencia</th><th>Uso</th></tr></thead>
                                    <tbody>
                                        <tr><td>Spinning ultraligera</td><td>1,8–2,1 m</td><td>UL (1–7 g)</td><td>Truchas, Percas pequeñas</td></tr>
                                        <tr><td>Spinning media</td><td>2,1–2,4 m</td><td>ML–M (7–21 g)</td><td>Black Bass, Lucio</td></tr>
                                        <tr><td>Spinning pesada</td><td>2,4–2,7 m</td><td>MH–H (21–60 g)</td><td>Lucio grande, Siluro</td></tr>
                                        <tr><td>Float rod</td><td>4,0–6,0 m</td><td>Ligera</td><td>Trucha, Barbo, Carpa</td></tr>
                                        <tr><td>Surfcasting</td><td>4,2–4,5 m</td><td>H–XH (100–250 g)</td><td>Pesca desde playa</td></tr>
                                        <tr><td>Carpfishing</td><td>3,6–3,9 m</td><td>2,75–3,5 lb TC</td><td>Carpa y grandes depred.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Materiales</div>
                            <p><strong>Carbono (grafito):</strong> Ligero, alta sensibilidad y rapidez de acción. Ideal para spinning y técnicas activas. Frágil en impactos fuertes.</p>
                            <p><strong>Fibra de vidrio:</strong> Más pesado pero muy resistente. Buena amortiguación para peces que pelean fuerte. Usado en float rods y carpfishing.</p>
                            <p><strong>Composite (mix):</strong> Equilibrio entre sensibilidad y resistencia. Muy popular en cañas de precio medio para uso versátil.</p>
                        </div>
                        <div class="guia-tip"><strong>Consejo:</strong> Para empezar, una caña spinning ML de 2,1 m con lanzado 7–21 g cubre el 80% de situaciones en embalse. Añade una float rod de 4,5 m si pescas río con frecuencia.</div>
                    </div>
                </div>
            </div>

            <div class="guia-card" id="eq-carretes">
                <button class="guia-card__header" onclick="toggleGuia('eq-carretes')" aria-expanded="false">
                    <div class="guia-card__icon guia-card__icon--amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <circle cx="11" cy="12" r="8"/>
                            <circle cx="11" cy="12" r="3.5"/>
                            <path d="M19 12 L22 12" stroke-linecap="round"/>
                            <path d="M22 9 L22 12" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="guia-card__meta">
                        <span class="guia-badge guia-badge--amber">Material</span>
                        <h3 class="guia-card__title">Carretes y Líneas</h3>
                        <p class="guia-card__subtitle">Spinning · Baitcasting · Líneas y tallas</p>
                    </div>
                    <svg class="guia-card__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="guia-card__body">
                    <div class="guia-content">
                        <div class="guia-section">
                            <div class="guia-section__title">Tipos de Carrete</div>
                            <p><strong>Carrete de freno frontal (spinning):</strong> El más versátil y fácil de usar. Talla 1000–2000 para UL, 2500–4000 para spinning media, 5000+ para surfcasting y carpa. El freno frontal ofrece mayor precisión que el trasero.</p>
                            <p><strong>Baitcasting:</strong> Alta precisión en lanzado de señuelos pesados. Curva de aprendizaje más pronunciada. Ideal para Texas rig y señuelos de 14+ g con Black Bass.</p>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Líneas</div>
                            <div class="guia-table-wrap">
                                <table class="guia-table">
                                    <thead><tr><th>Tipo</th><th>Ventaja</th><th>Uso ideal</th></tr></thead>
                                    <tbody>
                                        <tr><td>Trenzado (PE)</td><td>Alta resistencia, sin elongación</td><td>Spinning, jigging, surfcasting</td></tr>
                                        <tr><td>Monofilamento</td><td>Económico, buena amortiguación</td><td>Float rod, bajo de línea, pesca suave</td></tr>
                                        <tr><td>Fluorocarbono</td><td>Invisible en agua, resistente</td><td>Bajo de línea siempre</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="guia-tip"><strong>Setup óptimo:</strong> Llena el carrete de trenzado fino (PE 0,10–0,17) y añade siempre un bajo de fluorocarbono de 1,5–2 m. El trenzado da sensibilidad y el FC aporta invisibilidad y resistencia a la abrasión.</div>
                    </div>
                </div>
            </div>

            <div class="guia-card" id="eq-señuelos">
                <button class="guia-card__header" onclick="toggleGuia('eq-señuelos')" aria-expanded="false">
                    <div class="guia-card__icon guia-card__icon--amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <path d="M10 4 L14 4" stroke-linecap="round"/>
                            <path d="M12 4 L12 16 Q12 21 17 21 Q22 21 22 16 Q22 13 19 13" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="guia-card__meta">
                        <span class="guia-badge guia-badge--amber">Catálogo</span>
                        <h3 class="guia-card__title">Señuelos por Tipo de Agua</h3>
                        <p class="guia-card__subtitle">Hard lures · Soft lures · Topwater · Jigs</p>
                    </div>
                    <svg class="guia-card__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="guia-card__body">
                    <div class="guia-content">
                        <div class="guia-section">
                            <div class="guia-section__title">Señuelos Duros (Hard Lures)</div>
                            <div class="guia-chips">
                                <span class="guia-chip">Crankbait — 1–4 m de profundidad</span>
                                <span class="guia-chip">Jerkbait — aguas frías y claras</span>
                                <span class="guia-chip">Popper — superficie, alba y ocaso</span>
                                <span class="guia-chip">Minnow — imitación exacta de pez foraje</span>
                                <span class="guia-chip">Spinnerbait — aguas turbias o vegetación</span>
                            </div>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Señuelos Blandos (Soft Lures)</div>
                            <div class="guia-chips">
                                <span class="guia-chip">Vinil / Shad — todo terreno</span>
                                <span class="guia-chip">Grub — Texas Rig en fondos</span>
                                <span class="guia-chip">Crayfish — fondo rocoso, Black Bass</span>
                                <span class="guia-chip">Swimbait — imitación de pez</span>
                                <span class="guia-chip">Worm — fondos blandos, slow</span>
                            </div>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Metal Jigs — Costa y Offshore</div>
                            <p>Los jigs metálicos son los señuelos estrella del rockfishing en el Cantábrico. Pesan 20–120 g y llegan a grandes profundidades. La técnica del jigging — elevar y dejar caer — imita un pez herido y es devastadora con el abadejo y la lubina.</p>
                        </div>
                        <div class="guia-tip"><strong>Regla de colores:</strong> En agua clara usa colores naturales (oliva, marrón, transparente). En agua turbia, colores vivos (chartreuse, naranja, blanco). En agua fría y profunda, colores oscuros que contrastan bien desde abajo.</div>
                    </div>
                </div>
            </div>

        </div>{{-- .guias-grid --}}
    </section>

    {{-- ════════════════════════════════
         SECCIÓN 3 — POR ENTORNO
         Tarjetas grandes con gradiente
         ════════════════════════════════ --}}
    <section class="hub-section">
        <div class="hub-section__head hub-section__head--green">
            <div class="hub-section__eyebrow hub-section__eyebrow--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Hábitats y Entornos
            </div>
            <h2 class="hub-section__title">Pesca por Entorno</h2>
            <p class="hub-section__sub">Estrategias adaptadas a cada ecosistema: río, lago y costa.</p>
        </div>

        <div class="hub-env-grid">

            {{-- RÍO --}}
            <div class="hub-env-card hub-env-card--river" id="env-rio">
                <div class="hub-env-card__gradient"></div>
                <div class="hub-env-card__content">
                    <div class="hub-env-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>
                            <path d="M2 12c.6.5 1.2 1 2.5 1C7 13 7 11 9.5 11c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>
                        </svg>
                    </div>
                    <span class="hub-env-card__label">Ríos y Arroyos</span>
                    <h3 class="hub-env-card__title">Pesca en Río</h3>
                    <p class="hub-env-card__desc">Trucha, Barbo y Carpa. Técnica de flotador y cebo natural. Lee las corrientes, pozas y rápidos para encontrar a los peces.</p>
                    <div class="hub-env-card__species">
                        <span>Trucha</span><span class="hub-env-dot">·</span>
                        <span>Barbo</span><span class="hub-env-dot">·</span>
                        <span>Carpa</span>
                    </div>
                    <button class="hub-env-card__btn" onclick="toggleEnv('env-rio')">
                        <span>Más info</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                </div>
                <div class="hub-env-card__drawer">
                    <div class="guia-content" style="padding: 1.25rem 1.375rem">
                        <div class="guia-section">
                            <div class="guia-section__title">Dónde están los peces</div>
                            <p><strong>Pozas:</strong> Aguas profundas y tranquilas aguas abajo de un rápido. Refugio para barbo y carpa.</p>
                            <p><strong>Rápidos:</strong> La trucha se sitúa al final del rápido donde la corriente aminora y llega alimento concentrado.</p>
                            <p><strong>Obstáculos:</strong> Raíces, rocas y troncos sumergidos — refugio preferido de trucha grande.</p>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Cebos Naturales</div>
                            <div class="guia-chips">
                                <span class="guia-chip">Gusano de tierra</span>
                                <span class="guia-chip">Larva blanca</span>
                                <span class="guia-chip">Maíz cocido</span>
                                <span class="guia-chip">Masa de pan</span>
                                <span class="guia-chip">Hueva sintética</span>
                            </div>
                        </div>
                        <div class="guia-tip"><strong>Técnica:</strong> Aproximate siempre desde aguas abajo, contra la corriente. Evita proyectar tu sombra. La trucha es muy sensible a las vibraciones.</div>
                    </div>
                </div>
            </div>

            {{-- LAGO --}}
            <div class="hub-env-card hub-env-card--lake" id="env-lago">
                <div class="hub-env-card__gradient"></div>
                <div class="hub-env-card__content">
                    <div class="hub-env-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                            <circle cx="12" cy="12" r="9"/><path d="M12 3a4.5 4.5 0 0 0 0 9 4.5 4.5 0 0 0 0 9"/>
                            <path d="M3 12h18"/>
                        </svg>
                    </div>
                    <span class="hub-env-card__label">Lagos y Embalses</span>
                    <h3 class="hub-env-card__title">Pesca en Lago</h3>
                    <p class="hub-env-card__desc">Black Bass, Lucio y Carpa. Señuelo artificial y carpfishing. La termoclina y las estructuras son las claves para localizar peces.</p>
                    <div class="hub-env-card__species">
                        <span>Black Bass</span><span class="hub-env-dot">·</span>
                        <span>Lucio</span><span class="hub-env-dot">·</span>
                        <span>Carpa</span>
                    </div>
                    <button class="hub-env-card__btn" onclick="toggleEnv('env-lago')">
                        <span>Más info</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                </div>
                <div class="hub-env-card__drawer">
                    <div class="guia-content" style="padding: 1.25rem 1.375rem">
                        <div class="guia-section">
                            <div class="guia-section__title">Localización</div>
                            <p><strong>Termoclina (verano):</strong> El límite entre agua cálida y fría concentra a los depredadores entre 4–8 m. Usa escandallo para localizarla.</p>
                            <p><strong>Estructuras:</strong> Diques, puntas rocosas, entradas de arroyos y cañaverales son puntos calientes todo el año.</p>
                        </div>
                        <div class="guia-section">
                            <div class="guia-section__title">Mejores Señuelos</div>
                            <div class="guia-chips">
                                <span class="guia-chip">Crankbait flotante</span>
                                <span class="guia-chip">Texas Rig vinil</span>
                                <span class="guia-chip">Popper superficie</span>
                                <span class="guia-chip">Jerkbait agua fría</span>
                            </div>
                        </div>
                        <div class="guia-tip"><strong>Horario:</strong> El alba (6–9h) y el ocaso (19–21h) son los momentos de máxima actividad. Los depredadores ascienden a zonas someras a cazar.</div>
                    </div>
                </div>
            </div>

            {{-- MAR (tarjeta ancha) --}}
            <div class="hub-env-card hub-env-card--sea hub-env-card--wide" id="env-mar">
                <div class="hub-env-card__gradient"></div>
                <div class="hub-env-card__content hub-env-card__content--split">
                    <div class="hub-env-split">
                        <div class="hub-env-card__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                                <path d="M12 2v6m0 0a4 4 0 0 0 4 4h6m-10-4a4 4 0 0 1-4 4H2"/>
                                <path d="M2 18c2-2 4-3 6-3s4 1 6 3 4 3 6 3"/>
                            </svg>
                        </div>
                        <span class="hub-env-card__label">Mediterráneo — Surfcasting</span>
                        <h3 class="hub-env-card__title">Costa Mediterránea</h3>
                        <p class="hub-env-card__desc">Sin mareas significativas. El viento y el oleaje marcan la actividad. Dorada en otoño, Lubina todo el año.</p>
                        <div class="hub-env-card__species">
                            <span>Dorada</span><span class="hub-env-dot">·</span><span>Lubina</span><span class="hub-env-dot">·</span><span>Corvina</span>
                        </div>
                    </div>
                    <div class="hub-env-divider"></div>
                    <div class="hub-env-split">
                        <div class="hub-env-card__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="22" height="22">
                                <path d="M2 8c2.667-2 5.333-2 8 0s5.333 2 8 0"/>
                                <path d="M2 14c2.667-2 5.333-2 8 0s5.333 2 8 0"/>
                                <path d="M2 20c2.667-2 5.333-2 8 0s5.333 2 8 0"/>
                            </svg>
                        </div>
                        <span class="hub-env-card__label">Cantábrico — Rockfishing</span>
                        <h3 class="hub-env-card__title">Costa Cantábrica</h3>
                        <p class="hub-env-card__desc">Mareas de 3–5 m. Los cambios de marea activan a los depredadores. Señuelos ligeros desde roca para Sargo y Abadejo.</p>
                        <div class="hub-env-card__species">
                            <span>Sargo</span><span class="hub-env-dot">·</span><span>Abadejo</span><span class="hub-env-dot">·</span><span>Maragota</span>
                        </div>
                    </div>
                </div>
                <button class="hub-env-card__btn hub-env-card__btn--centered" onclick="toggleEnv('env-mar')">
                    <span>Ver comparativa completa</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="hub-env-card__drawer">
                    <div class="guia-content" style="padding: 1.25rem 1.375rem">
                        <div class="guia-section">
                            <div class="guia-table-wrap">
                                <table class="guia-table">
                                    <thead><tr><th>Factor</th><th>Mediterráneo</th><th>Cantábrico</th></tr></thead>
                                    <tbody>
                                        <tr><td>Mareas</td><td>0,2–0,4 m (casi nulas)</td><td>3–5 m, muy marcadas</td></tr>
                                        <tr><td>Técnica</td><td>Surfcasting desde playa</td><td>Rockfishing desde roca</td></tr>
                                        <tr><td>Señuelos</td><td>Cebo natural (calamar, sardina)</td><td>Metal jigs, vinils</td></tr>
                                        <tr><td>Pico temporada</td><td>Otoño (doradas a orilla)</td><td>Otoño-primavera</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="guia-tip"><strong>Seguridad Cantábrico:</strong> Usa botas de roca antideslizantes y consulta siempre el estado del mar antes de salir. Nunca pesques solo en escolleras expuestas con coeficientes altos.</div>
                    </div>
                </div>
            </div>

        </div>{{-- .hub-env-grid --}}
    </section>

    {{-- ════════════════════════════════
         SECCIÓN 4 — COMUNIDAD UGC
         Feed dinámico de tutoriales
         ════════════════════════════════ --}}
    <section class="hub-section">
        <div class="hub-section__head hub-section__head--community">
            <div class="hub-section__head-row">
                <div>
                    <div class="hub-section__eyebrow hub-section__eyebrow--community">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Saber colectivo
                    </div>
                    <h2 class="hub-section__title">
                        Tutoriales de la Comunidad
                        @if($tutoriales->isNotEmpty())
                            <span class="hub-count-badge">{{ $tutoriales->count() }}</span>
                        @endif
                    </h2>
                    <p class="hub-section__sub">Guías creadas por pescadores de la comunidad. Comparte tu conocimiento.</p>
                </div>
                <a href="{{ route('tutoriales.create') }}" class="btn btn--primary btn--sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" width="13" height="13">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Añadir tutorial
                </a>
            </div>
        </div>

        @if($tutoriales->isEmpty())
            <div class="hub-ugc-empty">
                <div class="hub-ugc-empty__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <p class="hub-ugc-empty__title">Sé el primero en compartir</p>
                <p class="hub-ugc-empty__sub">Ningún pescador ha publicado un tutorial todavía. ¿Conoces una técnica especial? ¡Compártela con la comunidad!</p>
                <a href="{{ route('tutoriales.create') }}" class="btn btn--primary">Escribir tutorial</a>
            </div>
        @else
            <div class="hub-ugc-grid">
                @foreach($tutoriales as $tutorial)
                    <div class="ugc-card-wrap">
                        <a href="{{ route('tutoriales.show', $tutorial) }}" class="ugc-card">
                            @if($tutorial->imagen_cabecera)
                                <div class="ugc-card__img-wrap">
                                    <img src="{{ asset('storage/' . $tutorial->imagen_cabecera) }}"
                                         alt="{{ $tutorial->titulo }}" class="ugc-card__img">
                                </div>
                            @else
                                <div class="ugc-card__img-wrap ugc-card__img-placeholder ugc-card__img-placeholder--{{ $tutorial->categoria }}">
                                    @if($tutorial->categoria === 'tecnica')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28">
                                            <line x1="4" y1="20" x2="19" y2="5" stroke-linecap="round"/>
                                            <circle cx="20.5" cy="3.5" r="1.75"/>
                                        </svg>
                                    @elseif($tutorial->categoria === 'equipo')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28">
                                            <circle cx="11" cy="12" r="8"/>
                                            <circle cx="11" cy="12" r="3.5"/>
                                            <path d="M19 12 L22 12" stroke-linecap="round"/>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28">
                                            <path d="M2 9 Q5.5 6 9 9 Q12.5 12 16 9 Q19.5 6 22 9" stroke-linecap="round" fill="none"/>
                                            <path d="M2 16 Q5.5 13 9 16 Q12.5 19 16 16 Q19.5 13 22 16" stroke-linecap="round" fill="none"/>
                                        </svg>
                                    @endif
                                </div>
                            @endif
                            <div class="ugc-card__body">
                                <span class="ugc-card__cat ugc-card__cat--{{ $tutorial->categoria }}">{{ $tutorial->categoriaLabel() }}</span>
                                <h3 class="ugc-card__title">{{ $tutorial->titulo }}</h3>
                                <p class="ugc-card__excerpt">{{ Str::limit($tutorial->contenido, 110) }}</p>
                                <div class="ugc-card__author">
                                    @if($tutorial->user->avatar)
                                        <img src="{{ asset('storage/' . $tutorial->user->avatar) }}"
                                             alt="{{ $tutorial->user->name }}" class="avatar avatar--sm">
                                    @else
                                        <div class="avatar avatar--sm avatar--placeholder" style="width:26px;height:26px;font-size:.75rem">
                                            {{ strtoupper(substr($tutorial->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $tutorial->user->name }}</span>
                                    <span class="ugc-card__dot">·</span>
                                    <span>{{ $tutorial->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                        @auth
                            @if(auth()->id() === $tutorial->user_id)
                                <form method="POST"
                                      action="{{ route('tutoriales.destroy', $tutorial) }}"
                                      class="ugc-delete-form"
                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este tutorial? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ugc-delete-btn" title="Eliminar tutorial" aria-label="Eliminar tutorial">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>{{-- .hub-wrapper --}}

<script>
function toggleTech(id) {
    var card = document.getElementById(id);
    var isOpen = card.classList.toggle('is-expanded');
    card.querySelector('.hub-tech-card__toggle').setAttribute('aria-expanded', isOpen);
    card.querySelector('.hub-tech-card__toggle-text').textContent = isOpen ? 'Cerrar' : 'Ver guía';
}

function toggleGuia(id) {
    var card = document.getElementById(id);
    var isOpen = card.classList.toggle('is-open');
    card.querySelector('.guia-card__header').setAttribute('aria-expanded', isOpen);
}

function toggleEnv(id) {
    var card = document.getElementById(id);
    card.classList.toggle('is-expanded');
}
</script>
@endsection
