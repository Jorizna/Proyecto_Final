@extends('layouts.app')

@section('title', 'Política de Privacidad')
@section('body-class', 'legal-bg')

@section('content')
<div class="legal-page">
    <div class="legal-card">

        <div class="legal-header">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('login') }}"
               class="legal-back link">
                ← Volver
            </a>
            <h1 class="legal-title">Política de Privacidad y Términos de Uso</h1>
            <p class="legal-meta">FishSpot Aragón &middot; Última actualización: mayo 2025</p>
        </div>

        <div class="legal-body">
        <section class="legal-section">
            <h2>1. Responsable del Tratamiento</h2>
            <table class="legal-table">
                <tr><th>Denominación</th><td>FishSpot Aragón (proyecto académico)</td></tr>
                <tr><th>Centro</th><td>CPIFP Los Enlaces, Zaragoza</td></tr>
                <tr><th>Finalidad del proyecto</th><td>Proyecto final del Ciclo Formativo DAW — Entornos de Desarrollo 2025/26</td></tr>
            </table>
            <p class="legal-note">
                Al ser un proyecto académico de demostración, no es de aplicación la obligación de designar un Delegado de Protección de Datos (DPD) según el art. 37 RGPD. En un despliegue real, debería designarse uno si el tratamiento así lo requiriese.
            </p>
        </section>
        <section class="legal-section">
            <h2>2. Datos Personales que se Recogen</h2>
            <p>Al registrarte y utilizar FishSpot Aragón, se tratan los siguientes datos:</p>
            <table class="legal-table">
                <thead><tr><th>Dato</th><th>Obligatorio</th><th>Descripción</th></tr></thead>
                <tbody>
                    <tr><td>Nombre de usuario</td><td>Sí</td><td>Nombre visible en el perfil y publicaciones.</td></tr>
                    <tr><td>Dirección de correo electrónico</td><td>Sí</td><td>Identificador único de la cuenta. No se muestra públicamente.</td></tr>
                    <tr><td>Contraseña</td><td>Sí</td><td>Se almacena <strong>exclusivamente como hash Bcrypt</strong> (12 rondas). Nadie puede recuperar tu contraseña original.</td></tr>
                    <tr><td>Foto de perfil (avatar)</td><td>No</td><td>Imagen de avatar subida voluntariamente. Redimensionada a 400×400 px.</td></tr>
                    <tr><td>Imagen de banner</td><td>No</td><td>Imagen de cabecera del perfil. Redimensionada a 1500 px de ancho.</td></tr>
                    <tr><td>Biografía (bio)</td><td>No</td><td>Texto libre de presentación del usuario.</td></tr>
                    <tr><td>Coordenadas GPS de zonas publicadas</td><td>Sí (al publicar una zona)</td><td>Latitud y longitud del punto de pesca. Son públicas para todos los usuarios registrados. No se recogen coordenadas de la ubicación del dispositivo.</td></tr>
                    <tr><td>Contenido generado</td><td>—</td><td>Textos, imágenes y comentarios publicados voluntariamente.</td></tr>
                    <tr><td>Metadatos de actividad</td><td>—</td><td>Likes, repostes, favoritos, seguidores, seguidos, notificaciones.</td></tr>
                </tbody>
            </table>
        </section>
        <section class="legal-section">
            <h2>3. Finalidad del Tratamiento</h2>
            <ul class="legal-list">
                <li><strong>Gestión de cuenta:</strong> crear, autenticar y mantener tu cuenta de usuario.</li>
                <li><strong>Funcionalidad social:</strong> mostrar publicaciones, comentarios, perfiles y notificaciones de actividad.</li>
                <li><strong>Mapa interactivo:</strong> geolocalizar y mostrar las zonas de pesca publicadas voluntariamente.</li>
                <li><strong>Comunicaciones internas:</strong> notificaciones dentro de la plataforma (likes, comentarios, nuevos seguidores). No se envían correos electrónicos de marketing.</li>
                <li><strong>Moderación de contenido:</strong> garantizar el cumplimiento de los términos de uso.</li>
            </ul>
        </section>
        <section class="legal-section">
            <h2>4. Base Legal del Tratamiento</h2>
            <table class="legal-table">
                <thead><tr><th>Tratamiento</th><th>Base legal (RGPD)</th></tr></thead>
                <tbody>
                    <tr><td>Registro y gestión de la cuenta</td><td>Art. 6.1.b — Ejecución de un contrato (relación de servicio).</td></tr>
                    <tr><td>Publicación de contenido y actividad social</td><td>Art. 6.1.a — Consentimiento explícito del usuario al registrarse y al publicar.</td></tr>
                    <tr><td>Moderación y seguridad</td><td>Art. 6.1.f — Interés legítimo del responsable del tratamiento.</td></tr>
                </tbody>
            </table>
        </section>
        <section class="legal-section">
            <h2>5. Plazo de Conservación</h2>
            <p>Los datos se conservarán <strong>mientras la cuenta permanezca activa</strong>. Al solicitar la eliminación de la cuenta, se borrarán todos los datos personales asociados, salvo aquellos que deban conservarse por obligaciones legales. En este entorno académico, los datos solo persisten en la base de datos local del contenedor Docker y desaparecen al destruir el contenedor.</p>
        </section>
        <section class="legal-section">
            <h2>6. Tus Derechos (RGPD / LOPDGDD)</h2>
            <p>Como usuario tienes los siguientes derechos sobre tus datos personales:</p>
            <ul class="legal-list">
                <li><strong>Acceso:</strong> conocer qué datos tuyos se tratan.</li>
                <li><strong>Rectificación:</strong> corregir datos inexactos. Puedes hacerlo desde <em>Perfil → Editar perfil</em>.</li>
                <li><strong>Supresión ("derecho al olvido"):</strong> solicitar el borrado de tus datos.</li>
                <li><strong>Portabilidad:</strong> recibir tus datos en un formato estructurado y de uso común.</li>
                <li><strong>Oposición:</strong> oponerte a determinados tratamientos.</li>
                <li><strong>Limitación:</strong> solicitar la limitación del tratamiento en ciertos supuestos.</li>
            </ul>
            <p>Puedes ejercer estos derechos contactando con el responsable del tratamiento. También tienes derecho a presentar una reclamación ante la <strong>Agencia Española de Protección de Datos (AEPD)</strong> en <a href="https://www.aepd.es" target="_blank" rel="noopener" class="link">www.aepd.es</a>.</p>
        </section>
        <section class="legal-section">
            <h2>7. Política de Cookies</h2>
            <p>FishSpot Aragón utiliza <strong>únicamente cookies técnicas de sesión</strong>, necesarias para el funcionamiento del servicio:</p>
            <table class="legal-table">
                <thead><tr><th>Cookie</th><th>Tipo</th><th>Finalidad</th><th>Duración</th></tr></thead>
                <tbody>
                    <tr><td><code>fishspot_session</code></td><td>Técnica / sesión</td><td>Mantener la sesión de usuario autenticado.</td><td>Sesión del navegador</td></tr>
                    <tr><td><code>XSRF-TOKEN</code></td><td>Técnica / seguridad</td><td>Protección contra ataques CSRF en formularios.</td><td>Sesión del navegador</td></tr>
                </tbody>
            </table>
            <p class="legal-note">No se utilizan cookies de seguimiento, publicidad, analíticas de terceros ni redes sociales externas.</p>
        </section>
        <section class="legal-section">
            <h2>8. Medidas de Seguridad</h2>
            <ul class="legal-list">
                <li><strong>Contraseñas:</strong> almacenadas como hash Bcrypt con factor de coste 12. Nadie, ni el administrador, puede ver tu contraseña original.</li>
                <li><strong>Protección CSRF:</strong> todos los formularios POST/PUT/DELETE incluyen un token de seguridad que previene envíos fraudulentos desde otros sitios.</li>
                <li><strong>Autorización:</strong> cada acción de edición/borrado verifica que el usuario es el propietario del recurso (Laravel Policies).</li>
                <li><strong>Validación:</strong> todas las entradas de usuario se validan y sanean antes de procesarse.</li>
                <li><strong>Imágenes:</strong> se valida el tipo MIME y el tamaño antes de almacenarlas.</li>
            </ul>
        </section>
        <section class="legal-section">
            <h2>9. Términos de Uso</h2>
            <h3>9.1 Uso aceptable</h3>
            <ul class="legal-list">
                <li>Está prohibido publicar contenido ilegal, ofensivo, difamatorio o que infrinja derechos de terceros.</li>
                <li>El usuario es responsable de la veracidad de las coordenadas GPS que publica.</li>
                <li>Está prohibido el acceso automatizado (bots, scrapers) sin autorización expresa.</li>
                <li>Queda prohibido compartir las credenciales de acceso con terceros.</li>
            </ul>
            <h3>9.2 Contenido publicado</h3>
            <p>Al publicar contenido (zonas, comentarios, tutoriales, imágenes), el usuario declara ser el titular o tener los derechos necesarios sobre dicho contenido, y autoriza a FishSpot Aragón a mostrarlo a otros usuarios registrados de la plataforma.</p>
            <h3>9.3 Moderación</h3>
            <p>Los usuarios con rol de <strong>moderador</strong> pueden eliminar publicaciones que incumplan estos términos. Las decisiones de moderación son definitivas en este entorno.</p>
            <h3>9.4 Menores de edad</h3>
            <p>Este servicio no está dirigido a menores de <strong>14 años</strong>. Al registrarte, declaras tener al menos 14 años o contar con el consentimiento de tu tutor legal, conforme al art. 8 RGPD y la LOPDGDD.</p>
        </section>
        <section class="legal-section">
            <h2>10. Cambios en esta Política</h2>
            <p>Esta política puede actualizarse. Los cambios se notificarán mediante un aviso visible en la plataforma. El uso continuado del servicio tras los cambios implica la aceptación de la nueva política.</p>
        </section>

        </div>

        <div class="legal-footer">
            <p>FishSpot Aragón &mdash; Proyecto académico CPIFP Los Enlaces &mdash; 2025/26</p>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('login') }}"
               class="btn btn--secondary">Volver</a>
        </div>

    </div>
</div>
@endsection
