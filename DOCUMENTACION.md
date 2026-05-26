# DOCUMENTACION TÉCNICA — FishSpot España

> Plataforma social de zonas de pesca para la comunidad pesquera española  
> Framework: Laravel 13 · PHP 8.4 · MariaDB 11.4 · Docker Compose

---

## ÍNDICE

1. [Descripción General](#1-descripción-general)
2. [Stack Tecnológico](#2-stack-tecnológico)
3. [Arquitectura del Sistema](#3-arquitectura-del-sistema)
4. [Requisitos Funcionales](#4-requisitos-funcionales)
5. [Requisitos No Funcionales](#5-requisitos-no-funcionales)
6. [Estructura de la Base de Datos](#6-estructura-de-la-base-de-datos)
7. [Capa de Modelos](#7-capa-de-modelos)
8. [Capa de Controladores](#8-capa-de-controladores)
9. [Capa de Vistas](#9-capa-de-vistas)
10. [Sistema de Rutas](#10-sistema-de-rutas)
11. [Políticas de Autorización](#11-políticas-de-autorización)
12. [Servicios y Jobs (Cola de Tareas)](#12-servicios-y-jobs)
13. [Infraestructura Docker](#13-infraestructura-docker)
14. [Casos de Uso](#14-casos-de-uso)
15. [Flujos de Usuario](#15-flujos-de-usuario)
16. [Sistema de Roles](#16-sistema-de-roles)
17. [Optimización y Rendimiento](#17-optimización-y-rendimiento)
18. [Elementos Obsoletos o Prescindibles](#18-elementos-obsoletos-o-prescindibles)

---

## 1. Descripción General

FishSpot España es una red social temática orientada a la comunidad pesquera española. Permite a los usuarios compartir zonas de pesca geolocalizadas, interactuar mediante likes, comentarios y repostes, seguir a otros usuarios y recibir notificaciones de actividad. Incluye un sistema de guías y tutoriales de elaboración propia, un mapa interactivo con todos los puntos publicados, y un rol de moderador con capacidad de eliminar contenido inapropiado.

La aplicación sigue el patrón arquitectónico **MVC** (Modelo-Vista-Controlador) implementado con el framework Laravel, desplegada en un entorno completamente contenedorizado con Docker Compose.

---

## 2. Stack Tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Backend | PHP + Laravel | 8.4 / 13.x |
| Base de datos | MariaDB | 11.4 |
| Servidor web | Nginx | Alpine |
| Caché y colas | Redis | 7-Alpine |
| Procesamiento de imágenes | Intervention Image | 4.x |
| Mapa interactivo | Leaflet.js + MarkerCluster | CDN |
| Frontend | CSS propio + Blade | — |
| Contenedorización | Docker + Docker Compose | — |
| Gestor de paquetes PHP | Composer | 2.x |

> **Sin frameworks CSS externos.** No se utiliza Bootstrap, Tailwind ni similar. Todo el diseño está implementado en `public/css/app.css` con variables CSS personalizadas.

---

## 3. Arquitectura del Sistema

### 3.1 Patrón MVC en Laravel

```
Petición HTTP
     │
     ▼
  Nginx (puerto 8001)
     │
     ▼
  PHP-FPM (pool de 50 workers)
     │
     ▼
  Router (routes/web.php + routes/auth.php)
     │
     ▼
  Middleware (auth, CSRF, etc.)
     │
     ▼
  Controlador → Modelo (Eloquent ORM) → MariaDB
     │                  │
     │            Redis (caché + sesiones)
     │
     ▼
  Vista Blade → HTML al cliente
```

### 3.2 Servicios adicionales

```
Cola de tareas (jobs)
     │
     ├─ Despachados por los controladores → Redis (driver: redis)
     └─ Consumidos por queue worker (contenedor fishspot_queue)
            └─ Ejecuta: php artisan queue:work redis
```

### 3.3 Estructura de directorios principal

```
proyecto_final/
├── app/
│   ├── Http/Controllers/       # Lógica de negocio
│   ├── Models/                 # Modelos Eloquent
│   ├── Policies/               # Autorización por recurso
│   ├── Jobs/                   # Tareas asíncronas
│   └── Services/               # Servicios reutilizables (ImageService)
├── database/
│   ├── migrations/             # Definición y evolución del esquema
│   └── seeders/                # Datos iniciales
├── resources/views/            # Plantillas Blade
├── routes/
│   ├── web.php                 # Rutas principales
│   └── auth.php                # Rutas de autenticación
├── public/
│   ├── css/app.css             # Estilos globales
│   └── images/                 # Assets estáticos
├── docker/                     # Configuración de Docker
│   ├── nginx/default.conf
│   ├── php/local.ini + www.conf
│   └── entrypoint.sh
└── docker-compose.yml
```

---

## 4. Requisitos Funcionales

### RF-01 Gestión de usuarios
- RF-01.1 El sistema permite el registro de nuevos usuarios con nombre, email y contraseña.
- RF-01.2 El sistema permite el inicio y cierre de sesión.
- RF-01.3 El usuario puede editar su perfil: nombre, email, bio, avatar y banner.
- RF-01.4 El usuario puede cambiar su contraseña desde el perfil.

### RF-02 Publicaciones de zonas de pesca
- RF-02.1 Un usuario autenticado puede crear una publicación con: título, descripción, coordenadas GPS, temporada recomendada, tipo de licencia requerida y hasta 8 imágenes.
- RF-02.2 El autor puede editar y eliminar sus propias publicaciones.
- RF-02.3 Las imágenes se comprimen automáticamente en servidor antes de almacenarse (máx. 1400px, calidad 82%). Los GIFs se almacenan sin modificar.
- RF-02.4 Las publicaciones pueden etiquetarse con especies de peces.
- RF-02.5 Todas las publicaciones son visibles en un mapa interactivo con agrupación por proximidad (MarkerCluster).

### RF-03 Feed principal
- RF-03.1 El feed muestra publicaciones de otros usuarios ordenadas por relevancia social.
- RF-03.2 Las publicaciones de usuarios seguidos, o con likes/repostes de usuarios seguidos, aparecen en primer lugar (prioridad tier-1).
- RF-03.3 El feed pagina de 20 en 20 publicaciones.
- RF-03.4 Las propias publicaciones del usuario autenticado no aparecen en su feed.

### RF-04 Interacciones sociales
- RF-04.1 Un usuario puede dar/quitar like a una publicación.
- RF-04.2 Un usuario puede repostear/des-repostear una publicación.
- RF-04.3 Un usuario puede guardar/quitar una publicación en favoritos.
- RF-04.4 Un usuario puede seguir/dejar de seguir a otros usuarios.
- RF-04.5 Un usuario puede comentar en una publicación, con soporte de respuestas anidadas hasta N niveles.
- RF-04.6 Los comentarios admiten hasta 4 imágenes adjuntas.
- RF-04.7 Un usuario puede eliminar sus propios comentarios.

### RF-05 Notificaciones
- RF-05.1 El sistema genera notificaciones para: likes, comentarios, favoritos, repostes, nuevos seguidores y nuevos tutoriales de usuarios seguidos.
- RF-05.2 Las notificaciones se procesan de forma asíncrona mediante una cola de tareas.
- RF-05.3 El usuario puede ver todas sus notificaciones y marcarlas como leídas.
- RF-05.4 No se generan notificaciones de acciones propias (auto-notificación deshabilitada).

### RF-06 Búsqueda
- RF-06.1 El usuario puede buscar publicaciones por título o descripción.
- RF-06.2 El usuario puede buscar otros usuarios por nombre o email.

### RF-07 Tutoriales y guías
- RF-07.1 Un usuario puede publicar tutoriales de pesca con título, categoría, contenido e imagen de cabecera.
- RF-07.2 Las categorías disponibles son: técnica, equipo y entorno.
- RF-07.3 El autor puede eliminar sus propios tutoriales.
- RF-07.4 Al publicar un tutorial, los seguidores del autor reciben una notificación.

### RF-08 Moderación
- RF-08.1 Un usuario con rol `moderador` puede eliminar cualquier publicación de cualquier usuario.
- RF-08.2 El moderador ve el botón "Eliminar zona" en todas las publicaciones, pero no el botón "Editar" (reservado al autor).
- RF-08.3 La autorización se verifica tanto en la vista (Blade) como en el backend (Policy).

### RF-09 Zonas guardadas
- RF-09.1 El usuario dispone de una sección dedicada con todas sus publicaciones marcadas como favoritas.

### RF-10 Filtrado del mapa interactivo
- RF-10.1 El sistema DEBE exponer un endpoint `GET /zonas/filtrar` que acepte los parámetros opcionales `etiquetas[]` (array de IDs), `temporada` (ENUM) y `licencia` (ENUM), devolviendo un JSON con las publicaciones que cumplen los criterios.
- RF-10.2 Los filtros DEBEN combinarse con lógica AND entre categorías distintas (etiqueta Y temporada) y OR dentro de la misma categoría (Carpa O Trucha).
- RF-10.3 El mapa DEBE actualizarse sin recarga de página: el JavaScript de Leaflet escucha los cambios de filtro, hace una llamada `fetch()` al endpoint, limpia las capas con `markerCluster.clearLayers()` y añade los nuevos marcadores.
- RF-10.4 Si la respuesta devuelve un array vacío, el mapa DEBE mostrar el mensaje: _"Ninguna zona coincide con los filtros seleccionados"_.
- RF-10.5 El estado de los filtros activos DEBE reflejarse en la URL como query params (p. ej. `?etiquetas[]=3&temporada=verano`) para permitir compartir o recargar la búsqueda con el mismo filtrado.
- RF-10.6 Al eliminar todos los filtros, el sistema DEBE restablecer la vista completa del mapa sin recargar la página.
- RF-10.7 El endpoint DEBE devolver `422 Unprocessable Entity` si algún valor de `temporada` o `licencia` no pertenece al ENUM definido en el modelo `Publicacion`.

### RF-11 Búsqueda geoespacial por proximidad
- RF-11.1 El sistema DEBE implementar un scope Eloquent `Publicacion::scopeNearCoordinates($query, float $lat, float $lng, int $radiusKm)` usando la fórmula de Haversine en SQL raw. Este scope DEBE ser la única fuente de verdad para la lógica de distancia en el codebase.
- RF-11.2 El endpoint `GET /buscar/cerca` DEBE aceptar los parámetros `lat`, `lng` y `radio` (entero en km). El servidor DEBE validar: `lat` en el rango [-90, 90], `lng` en [-180, 180] y `radio` dentro del conjunto `[5, 10, 25, 50]`.
- RF-11.3 Los resultados DEBEN incluir el campo calculado `distancia_km` y estar ordenados por distancia ascendente.
- RF-11.4 Si no hay resultados dentro del radio especificado, el JSON DEBE incluir un campo `sugerencia_radio` con el siguiente nivel disponible.
- RF-11.5 La geolocalización del dispositivo DEBE solicitarse mediante `navigator.geolocation.getCurrentPosition()` en el cliente. Las coordenadas NO DEBEN almacenarse en el servidor; se usan exclusivamente como parámetros efímeros de la petición de búsqueda.
- RF-11.6 Si el usuario deniega la geolocalización, el sistema DEBE permitir seleccionar el punto de búsqueda manualmente haciendo clic en el mapa.
- RF-11.7 La búsqueda geoespacial DEBE poder combinarse con los filtros de etiqueta, temporada y licencia del RF-10 en una única query.
- RF-11.8 La tabla `publicaciones` DEBE tener un índice compuesto sobre `(latitud, longitud)` para optimizar las queries de Haversine: `CREATE INDEX idx_pub_coords ON publicaciones (latitud, longitud)`.

---

## 5. Requisitos No Funcionales

### RNF-01 Rendimiento
- RNF-01.1 El cálculo de relevancia del feed (tier-1 IDs) se cachea en Redis con TTL de 120 segundos para evitar queries repetidas.
- RNF-01.2 PHP-FPM se configura en modo `dynamic` con un máximo de 50 workers concurrentes.
- RNF-01.3 Las sesiones de usuario se almacenan en Redis (no en disco), reduciendo latencia de I/O.
- RNF-01.4 Las notificaciones se procesan de forma asíncrona mediante un worker dedicado, sin bloquear el ciclo de petición-respuesta.
- RNF-01.5 El feed principal DEBE responder en menos de 400 ms bajo carga normal (hasta 100 usuarios concurrentes), incluyendo la resolución de caché Redis y la query paginada, medido en el percentil 95.
- RNF-01.6 La query geoespacial con Haversine DEBE completarse en menos de 500 ms para tablas de hasta 50.000 publicaciones, siempre que exista el índice compuesto sobre `(latitud, longitud)`.
- RNF-01.7 Para colecciones de más de 200 marcadores, el mapa DEBE cargar los datos vía `fetch()` asíncrono al endpoint `/zonas/filtrar` en lugar de embeber el JSON en la plantilla Blade, para reducir el tiempo hasta el primer render.
- RNF-01.8 Todas las imágenes en el feed DEBEN incluir el atributo `loading="lazy"` y dimensiones explícitas `width`/`height` para evitar el Cumulative Layout Shift (CLS) en conexiones lentas.

### RNF-02 Seguridad
- RNF-02.1 Todas las rutas de escritura requieren autenticación mediante middleware `auth`.
- RNF-02.2 Todas las peticiones POST/PUT/DELETE están protegidas con tokens CSRF.
- RNF-02.3 Las operaciones de edición/eliminación verifican propiedad del recurso mediante Laravel Policies.
- RNF-02.4 Las contraseñas se almacenan con hash Bcrypt (12 rondas).
- RNF-02.5 Las subidas de imágenes validan MIME type, extensión y tamaño antes de procesarse.
- RNF-02.6 Los inputs de usuario se sanean mediante el sistema de validación de Laravel antes de persistir.
- RNF-02.7 Las coordenadas GPS obtenidas de `navigator.geolocation` en el cliente NUNCA DEBEN almacenarse en el servidor ni en logs. Se usan exclusivamente como parámetros efímeros de la petición de búsqueda.
- RNF-02.8 Las coordenadas GPS enviadas en peticiones DEBEN validarse en servidor con reglas `numeric|between:-90,90` (latitud) y `numeric|between:-180,180` (longitud). Un valor fuera de rango DEBE retornar `422 Unprocessable Entity`.

### RNF-03 Escalabilidad
- RNF-03.1 La arquitectura basada en contenedores permite escalar servicios de forma independiente.
- RNF-03.2 El sistema de colas Redis permite añadir workers adicionales sin modificar el código.
- RNF-03.3 El almacenamiento de imágenes usa el sistema de Storage de Laravel, preparado para migración a S3 o similar.

### RNF-04 Disponibilidad
- RNF-04.1 Todos los servicios Docker están configurados con `restart: unless-stopped`.
- RNF-04.2 El contenedor de base de datos tiene health check antes de que la aplicación inicie.
- RNF-04.3 Nginx usa resolución DNS dinámica (`resolver 127.0.0.11`) para recuperarse automáticamente ante reinicios del contenedor PHP.

### RNF-05 Mantenibilidad
- RNF-05.1 El esquema de base de datos evoluciona mediante migraciones versionadas.
- RNF-05.2 Los datos de prueba se generan con seeders reproducibles.
- RNF-05.3 La compresión de imágenes está encapsulada en `ImageService`, aislando la dependencia de Intervention Image del resto de la aplicación.
- RNF-05.4 El scope `Publicacion::scopeNearCoordinates()` DEBE ser la única fuente de verdad para la lógica Haversine en todo el codebase; está prohibido duplicar la fórmula SQL en controladores o vistas.
- RNF-05.5 La aplicación DEBE exponer un endpoint `GET /health` (sin middleware `auth`) que devuelva `200 OK` con JSON `{"db": "ok", "redis": "ok"}` tras verificar conectividad a MariaDB y Redis. Este endpoint DEBE usarse como health check del servicio `app` en Docker Compose.
- RNF-05.6 El contenedor `fishspot_queue` DEBE exponer un health check que verifique el número de jobs encolados. Si supera 1.000 jobs sin procesar, DEBE escribirse una alerta en el log del contenedor.
- RNF-05.7 El Job `EnviarNotificacion` DEBE registrar en el canal `stack` de Laravel cada intento fallido tras agotar los 3 reintentos, incluyendo el tipo de notificación, `destinatario_id`, `actor_id` y el mensaje de excepción. El nivel de log DEBE ser `error`.

### RNF-06 Usabilidad
- RNF-06.1 La interfaz es responsive y funciona en dispositivos móviles y escritorio.
- RNF-06.2 El mapa interactivo agrupa marcadores automáticamente para evitar saturación visual.
- RNF-06.3 La paginación del feed usa un componente personalizado con navegación Anterior/Siguiente.
- RNF-06.4 El panel de filtros del mapa DEBE funcionar mediante tap en pantallas táctiles sin depender de eventos `hover`. Los elementos interactivos DEBEN tener un área mínima de toque de 44×44 px (criterio WCAG 2.5.5).
- RNF-06.5 Cualquier funcionalidad principal (publicar zona, ver mapa, buscar, ver notificaciones) DEBE ser accesible en un máximo de 3 clics desde la pantalla de inicio.
- RNF-06.6 Los errores de validación DEBEN mostrarse inline junto al campo que los produce, no únicamente como bloque de errores global al inicio del formulario.
- RNF-06.7 Si el usuario deniega la geolocalización al usar la búsqueda por proximidad, el sistema DEBE mostrar un mensaje explicativo y ofrecer la alternativa de seleccionar el punto manualmente en el mapa, sin bloquear el flujo.

---

## 6. Estructura de la Base de Datos

### 6.1 Diagrama Entidad-Relación

```
┌─────────────────┐
│      users      │
│─────────────────│
│ id (PK)         │◄──────────────────────────────────────────┐
│ name            │                                            │
│ email           │                                            │
│ password        │                                            │
│ avatar          │                                            │
│ banner          │                                            │
│ bio             │                                            │
│ rol             │ ← ENUM('user','moderador')                 │
│ created_at      │                                            │
└────────┬────────┘                                            │
         │                                                     │
         │ 1:N                                                 │
         ├──────────────────────────────┐                      │
         │                             │                       │
         ▼                             ▼                       │
┌────────────────────┐      ┌──────────────────┐              │
│   publicaciones    │      │    tutoriales     │              │
│────────────────────│      │──────────────────│              │
│ id (PK)            │      │ id (PK)           │              │
│ user_id (FK)       │      │ user_id (FK)      │              │
│ titulo             │      │ titulo            │              │
│ descripcion        │      │ categoria         │              │
│ latitud            │      │ contenido         │              │
│ longitud           │      │ imagen_cabecera   │              │
│ temporada          │      │ created_at        │              │
│ licencia           │      └──────────────────┘              │
│ created_at         │                                         │
└──────┬─────────────┘                                         │
       │                                                       │
       │ 1:N              ┌──────────────────────────┐         │
       ├─────────────────►│       comentarios        │         │
       │                  │──────────────────────────│         │
       │                  │ id (PK)                  │         │
       │                  │ publicacion_id (FK)       │         │
       │                  │ user_id (FK)  ────────────────────►│
       │                  │ parent_id (FK autorreferencia)     │
       │                  │ texto                    │         │
       │                  │ created_at               │         │
       │                  └──────────┬───────────────┘         │
       │                             │ 1:N                     │
       │                             ▼                         │
       │                  ┌──────────────────────┐             │
       │                  │  imagenes_comentario  │             │
       │                  │──────────────────────│             │
       │                  │ id (PK)               │             │
       │                  │ comentario_id (FK)    │             │
       │                  │ ruta                  │             │
       │                  │ orden                 │             │
       │                  └──────────────────────┘             │
       │                                                       │
       │ 1:N                                                   │
       ├─────────────────►┌──────────────────┐                 │
       │                  │     imagenes     │                 │
       │                  │──────────────────│                 │
       │                  │ id (PK)           │                 │
       │                  │ publicacion_id(FK)│                 │
       │                  │ ruta              │                 │
       │                  │ orden             │                 │
       │                  └──────────────────┘                 │
       │                                                       │
       │ N:M (pivot: publicacion_etiqueta)                     │
       ├─────────────────►┌──────────────────┐                 │
       │                  │    etiquetas     │                 │
       │                  │──────────────────│                 │
       │                  │ id (PK)           │                 │
       │                  │ nombre            │                 │
       │                  └──────────────────┘                 │
       │                                                       │
       │ 1:N                                                   │
       ├─────────────────►┌──────────────────┐                 │
       │                  │      likes       │◄────────────────┤
       │                  │──────────────────│                 │
       │                  │ id (PK)           │                 │
       │                  │ publicacion_id(FK)│                 │
       │                  │ user_id (FK)      │                 │
       │                  └──────────────────┘                 │
       │                                                       │
       │ 1:N                                                   │
       ├─────────────────►┌──────────────────┐                 │
       │                  │    repostes      │◄────────────────┤
       │                  │──────────────────│                 │
       │                  │ id (PK)           │                 │
       │                  │ publicacion_id(FK)│                 │
       │                  │ user_id (FK)      │                 │
       │                  └──────────────────┘                 │
       │                                                       │
       │ 1:N                                                   │
       └─────────────────►┌──────────────────┐                 │
                          │    favoritos     │◄────────────────┤
                          │──────────────────│                 │
                          │ id (PK)           │                 │
                          │ publicacion_id(FK)│                 │
                          │ user_id (FK)      │                 │
                          └──────────────────┘                 │
                                                               │
          ┌────────────────────────────────────────────────────┘
          │
          │ N:M (pivot: follows)
          ▼
┌──────────────────────┐    ┌──────────────────────────┐
│   follows (pivot)    │    │     notificaciones        │
│──────────────────────│    │──────────────────────────│
│ follower_id (FK)     │    │ id (PK)                   │
│ following_id (FK)    │    │ user_id (FK) ← destinatario│
│ created_at           │    │ actor_id (FK) ← origen    │
└──────────────────────┘    │ tipo (ENUM)               │
                            │ publicacion_id (FK, null) │
                            │ tutorial_id (FK, null)    │
                            │ leida (boolean)           │
                            │ created_at                │
                            └──────────────────────────┘

          ┌──────────────────────────────────┐
          │   jobs (cola Redis → tabla aux)  │
          │──────────────────────────────────│
          │ id (PK)                          │
          │ queue                            │
          │ payload (JSON serializado)       │
          │ attempts                         │
          │ reserved_at / available_at       │
          │ created_at                       │
          └──────────────────────────────────┘
```

### 6.2 Descripción de tablas

| Tabla | Propósito | Relaciones clave |
|---|---|---|
| `users` | Usuarios registrados. Contiene rol (`user`/`moderador`), avatar, banner y bio. | Raíz de casi todas las relaciones |
| `publicaciones` | Zona de pesca publicada. Almacena coordenadas GPS, temporada y tipo de licencia. | `users` (1:N), `etiquetas` (N:M), `comentarios` (1:N) |
| `comentarios` | Respuestas a publicaciones. `parent_id` nullable permite anidamiento infinito. | `publicaciones` (N:1), autorreferencia por `parent_id` |
| `imagenes` | Rutas de imágenes asociadas a publicaciones. | `publicaciones` (N:1) |
| `imagenes_comentario` | Rutas de imágenes adjuntas a comentarios. | `comentarios` (N:1) |
| `etiquetas` | Catálogo de especies de pesca. | `publicaciones` (N:M via pivot) |
| `publicacion_etiqueta` | Tabla pivote N:M entre publicaciones y etiquetas. | — |
| `likes` | Registro de likes. Unique por `(user_id, publicacion_id)`. | `users` + `publicaciones` |
| `repostes` | Registro de repostes. | `users` + `publicaciones` |
| `favoritos` | Publicaciones guardadas por el usuario. | `users` + `publicaciones` |
| `follows` | Relación seguidor/seguido. Unique por `(follower_id, following_id)`. | `users` autorreferencia |
| `notificaciones` | Eventos de actividad social. `tipo` ENUM controla el mensaje generado. | `users` (actor + destinatario), `publicaciones`, `tutoriales` |
| `tutoriales` | Guías de pesca generadas por usuarios. | `users` (1:N) |
| `jobs` | Cola de tareas pendientes (driver `database` como respaldo; en producción usa Redis). | — |

---

## 7. Capa de Modelos

Los modelos Eloquent se ubican en `app/Models/` y representan cada tabla de la base de datos. Gestionan relaciones, atributos y lógica de dominio básica.

### 7.1 `User`
**Archivo:** `app/Models/User.php`

Extiende `Authenticatable` de Laravel, que añade el sistema de autenticación.

```php
protected $fillable = ['name', 'email', 'password', 'avatar', 'banner', 'bio', 'rol'];

public function esModerador(): bool
{
    return $this->rol === 'moderador';
}
```

**Relaciones:**

| Método | Tipo | Descripción |
|---|---|---|
| `publicaciones()` | HasMany | Zonas publicadas por el usuario |
| `tutoriales()` | HasMany | Tutoriales redactados |
| `comentarios()` | HasMany | Comentarios emitidos |
| `likes()` | HasMany | Likes dados |
| `repostes()` | HasMany | Repostes realizados |
| `favoritos()` | HasMany | Guardados del usuario |
| `publicacionesFavoritas()` | BelongsToMany | Via tabla `favoritos` |
| `publicacionesLiked()` | BelongsToMany | Via tabla `likes` |
| `following()` | BelongsToMany | Usuarios que este sigue |
| `followers()` | BelongsToMany | Usuarios que le siguen |

### 7.2 `Publicacion`
**Archivo:** `app/Models/Publicacion.php`

Incluye constantes de dominio para temporadas y tipos de licencia, usadas tanto en validación como en vistas.

```php
const TEMPORADAS = ['invierno' => 'Invierno / Aguas Frías', ...];
const LICENCIAS  = ['mar' => 'Licencia de Mar / Costa', ...];
```

**Métodos auxiliares:**
- `temporadaLabel()` — devuelve la etiqueta legible de la temporada.
- `licenciaLabel()` — devuelve la etiqueta legible del tipo de licencia.
- `imagenPrincipal()` — devuelve la primera imagen asociada.

**Relaciones:** `user`, `comentarios`, `imagenes`, `likes`, `repostes`, `favoritos`, `etiquetas`.

### 7.3 `Comentario`
**Archivo:** `app/Models/Comentario.php`

Soporta anidamiento mediante autorreferencia con `parent_id`.

```php
public function children(): HasMany
{
    return $this->hasMany(Comentario::class, 'parent_id');
}
public function parent(): BelongsTo
{
    return $this->belongsTo(Comentario::class, 'parent_id');
}
```

### 7.4 `Notificacion`
**Archivo:** `app/Models/Notificacion.php`

Contiene la lógica de presentación de cada tipo de evento social.

```php
public function textoHtml(): string
{
    return match ($this->tipo) {
        'like'       => "A {$actor} le ha gustado tu zona {$zona}",
        'comentario' => "{$actor} ha respondido en tu zona {$zona}",
        'favorito'   => "{$actor} ha compartido tu publicación {$zona}",
        'tutorial'   => "{$actor} ha publicado un nuevo tutorial: {$tuto}",
        'seguir'     => "{$actor} ha comenzado a seguirte",
    };
}
```

El método estático `crear()` actúa como factory con protección anti-autonotificación:

```php
public static function crear(int $userId, int $actorId, string $tipo, ...): void
{
    if ($userId === $actorId) return; // No notificarse a sí mismo
    self::create([...]);
}
```

### 7.5 Otros modelos

| Modelo | Tabla | Nota |
|---|---|---|
| `Imagen` | `imagenes` | Ruta y orden de imagen de publicación |
| `Like` | `likes` | Sin lógica adicional |
| `Reposte` | `repostes` | Sin lógica adicional |
| `Favorito` | `favoritos` | Sin lógica adicional |
| `Etiqueta` | `etiquetas` | Solo `nombre` |
| `Tutorial` | `tutoriales` | `categoria` ENUM: técnica/equipo/entorno |

---

## 8. Capa de Controladores

Los controladores se ubican en `app/Http/Controllers/` y contienen la lógica de negocio de cada sección.

### 8.1 `PublicacionController`
**Archivo:** `app/Http/Controllers/PublicacionController.php`

Es el controlador más complejo del sistema.

| Método | Ruta | Descripción |
|---|---|---|
| `index()` | `GET /` | Feed principal con priorización por follows. Cachea los IDs tier-1 en Redis (120s). |
| `buscar()` | `GET /buscar` | Búsqueda full-text por título y descripción. |
| `create()` | `GET /zonas/crear` | Formulario de nueva publicación. |
| `store()` | `POST /zonas` | Valida, persiste y comprime imágenes. Invalida caché del feed. |
| `show()` | `GET /zonas/{pub}` | Vista detalle con comentarios anidados cargados eager. |
| `edit()` | `GET /zonas/{pub}/editar` | Formulario de edición (solo autor, vía Policy). |
| `update()` | `PUT /zonas/{pub}` | Actualiza datos e imágenes adicionales. |
| `destroy()` | `DELETE /zonas/{pub}` | Elimina publicación, imágenes del disco y cachés. |
| `destroyImagen()` | `DELETE /zonas/{pub}/imagenes/{img}` | Elimina una imagen individual. |

**Lógica del feed con prioridad:**
```php
// Cachea en Redis solo los IDs (string), no el paginador
$inList = Cache::tags('feed')->remember("feed.tier1.{$user->id}", 120, function () use ($followingIds) {
    $tier1Ids = Publicacion::whereIn('user_id', $followingIds)->pluck('id')
        ->merge(Like::whereIn('user_id', $followingIds)->pluck('publicacion_id'))
        ->merge(Reposte::whereIn('user_id', $followingIds)->pluck('publicacion_id'))
        ->unique()->values()->toArray();
    return implode(',', $tier1Ids ?: [0]);
});

$publicaciones = Publicacion::...
    ->orderByRaw("CASE WHEN id IN ({$inList}) THEN 0 ELSE 1 END")
    ->latest()->paginate(20);
```

### 8.2 `ComentarioController`
**Archivo:** `app/Http/Controllers/ComentarioController.php`

| Método | Descripción |
|---|---|
| `store()` | Crea comentario (o respuesta si lleva `parent_id`), adjunta imágenes, despacha notificación asíncrona. |
| `destroy()` | Elimina comentario propio. |

### 8.3 `LikeController`
**Archivo:** `app/Http/Controllers/LikeController.php`

Implementa toggle: si existe el like lo elimina, si no lo crea y despacha `EnviarNotificacion`.

### 8.4 `ReposteController`
**Archivo:** `app/Http/Controllers/ReposteController.php`

Igual que `LikeController` pero sin notificación (el reposte es una acción silenciosa para el autor).

### 8.5 `FavoritoController`
**Archivo:** `app/Http/Controllers/FavoritoController.php`

| Método | Descripción |
|---|---|
| `index()` | Lista todas las publicaciones guardadas por el usuario autenticado. |
| `toggle()` | Añade o elimina de favoritos, despacha notificación al autor. |

### 8.6 `FollowController`
**Archivo:** `app/Http/Controllers/FollowController.php`

Previene que un usuario se siga a sí mismo. Al seguir, despacha notificación tipo `seguir`.

### 8.7 `PerfilController`
**Archivo:** `app/Http/Controllers/PerfilController.php`

| Método | Descripción |
|---|---|
| `show()` | Carga el perfil propio con feed combinado (publicaciones + repostes) ordenado por fecha. |
| `edit()` | Formulario de edición de perfil. |
| `update()` | Actualiza datos, procesa avatar (`ImageService::storeAvatar`) y banner (`ImageService::storeBanner`). |
| `updatePassword()` | Verifica contraseña actual antes de cambiarla. |

### 8.8 `UsuarioController`
**Archivo:** `app/Http/Controllers/UsuarioController.php`

| Método | Descripción |
|---|---|
| `show()` | Perfil público de cualquier usuario: publicaciones, respuestas y likes en tabs. |
| `buscar()` | Búsqueda de usuarios por nombre o email (máx. 30 resultados). |

### 8.9 `NotificacionController`
**Archivo:** `app/Http/Controllers/NotificacionController.php`

| Método | Descripción |
|---|---|
| `index()` | Lista notificaciones del usuario, separa leídas/no leídas. |
| `markAllRead()` | Marca todas las notificaciones como leídas. |

### 8.10 `TutorialController`
**Archivo:** `app/Http/Controllers/TutorialController.php`

| Método | Descripción |
|---|---|
| `index()` | Lista todos los tutoriales. |
| `create()` | Formulario de nuevo tutorial. |
| `store()` | Persiste tutorial, comprime imagen cabecera, despacha notificación a seguidores. |
| `show()` | Vista detalle del tutorial. |
| `destroy()` | Elimina tutorial y su imagen. Solo el autor puede eliminar. |

### 8.11 Controladores de autenticación (`Auth/`)

| Controlador | Responsabilidad |
|---|---|
| `RegisteredUserController` | Registro de nuevos usuarios. |
| `AuthenticatedSessionController` | Login y logout. |
| `ConfirmablePasswordController` | Confirmación de contraseña para acciones sensibles. |

---

## 9. Capa de Vistas

Las vistas se ubican en `resources/views/` y usan el motor de plantillas **Blade** de Laravel.

### 9.1 Layout principal
**Archivo:** `resources/views/layouts/app.blade.php`

Plantilla base de toda la aplicación autenticada. Define:
- `<head>` con favicon, meta tags y estilos.
- Sidebar izquierdo con navegación principal (bento design).
- Columna central con `@yield('content')`.
- Barra de notificaciones en el sidebar.
- Scripts globales: Leaflet, dropdown JS, menú móvil.

El sidebar muestra el contador de notificaciones no leídas en tiempo de carga.

### 9.2 Vistas de publicaciones

| Archivo | Descripción |
|---|---|
| `publicaciones/index.blade.php` | Feed principal: tarjetas horizontales compactas (texto + thumbnail 88×88px), paginación personalizada. |
| `publicaciones/show.blade.php` | Vista detalle: cabecera, galería de fotos estilo Twitter (grids de 1-4 imágenes), barra de interacciones, hilo de comentarios anidados, mapa Leaflet de la zona. |
| `publicaciones/create.blade.php` | Formulario de nueva zona: mapa clickable para selección de coordenadas, previsualización de imágenes, selector de etiquetas. |
| `publicaciones/edit.blade.php` | Igual que create pero con datos pre-rellenados y gestión de imágenes existentes. |
| `publicaciones/buscar.blade.php` | Resultados de búsqueda con filtros por etiqueta, temporada y licencia. |
| `publicaciones/_reply.blade.php` | Partial reutilizable: renderiza un comentario y sus hijos de forma recursiva. |

### 9.3 Vistas de perfil y usuarios

| Archivo | Descripción |
|---|---|
| `usuarios/show.blade.php` | Perfil público (y propio): banner, avatar solapado, bio, estadísticas (zonas/seguidores/seguidos), tres tabs (Publicaciones, Respuestas, Me gusta). |
| `usuarios/buscar.blade.php` | Resultados de búsqueda de usuarios con avatar y bio. |
| `perfil/edit.blade.php` | Formulario de edición de perfil propio: datos personales, avatar, banner y contraseña. |

### 9.4 Vistas de autenticación

| Archivo | Descripción |
|---|---|
| `auth/login.blade.php` | Formulario de inicio de sesión. |
| `auth/register.blade.php` | Formulario de registro. |
| `auth/confirm-password.blade.php` | Confirmación de contraseña para acciones sensibles. |
| `auth/verify-email.blade.php` | ⚠️ Existe pero la verificación de email no está activa (ver §18). |

### 9.5 Otras vistas

| Archivo | Descripción |
|---|---|
| `notificaciones/index.blade.php` | Lista de notificaciones con badge de color por tipo: azul (like), verde (comentario), morado (seguir), naranja (favorito), gris (tutorial). |
| `guardados/index.blade.php` | Grid de publicaciones guardadas como favoritas. |
| `guias/index.blade.php` | Listado de tutoriales con filtro por categoría. |
| `guias/show.blade.php` | Vista detalle de tutorial con imagen de cabecera. |
| `guias/create.blade.php` | Formulario de nuevo tutorial. |
| `welcome.blade.php` | Página de aterrizaje para usuarios no autenticados. |
| `pagination/fishspot.blade.php` | Componente de paginación personalizado con Anterior/Siguiente y contador "Página X de Y". |

---

## 10. Sistema de Rutas

**Archivos:** `routes/web.php` y `routes/auth.php`

Todas las rutas de la aplicación (excepto login, registro y la raíz) están protegidas con el middleware `auth`.

### 10.1 Rutas públicas

| Método | URI | Nombre | Controlador |
|---|---|---|---|
| GET | `/` | `publicaciones.index` | `PublicacionController@index` |
| GET | `/login` | `login` | `AuthenticatedSessionController@create` |
| POST | `/login` | — | `AuthenticatedSessionController@store` |
| GET | `/register` | `register` | `RegisteredUserController@create` |
| POST | `/register` | — | `RegisteredUserController@store` |

### 10.2 Rutas protegidas (middleware `auth`)

| Método | URI | Nombre | Controlador |
|---|---|---|---|
| GET | `/buscar` | `buscar` | `PublicacionController@buscar` |
| GET | `/u` | `usuarios.buscar` | `UsuarioController@buscar` |
| GET | `/u/{user}` | `usuarios.show` | `UsuarioController@show` |
| POST | `/u/{user}/seguir` | `follows.toggle` | `FollowController@toggle` |
| GET | `/perfil` | `perfil.show` | `PerfilController@show` |
| GET | `/perfil/editar` | `perfil.edit` | `PerfilController@edit` |
| PUT | `/perfil` | `perfil.update` | `PerfilController@update` |
| PUT | `/perfil/contrasena` | `perfil.password` | `PerfilController@updatePassword` |
| GET | `/zonas/crear` | `publicaciones.create` | `PublicacionController@create` |
| POST | `/zonas` | `publicaciones.store` | `PublicacionController@store` |
| GET | `/zonas/{pub}` | `publicaciones.show` | `PublicacionController@show` |
| GET | `/zonas/{pub}/editar` | `publicaciones.edit` | `PublicacionController@edit` |
| PUT | `/zonas/{pub}` | `publicaciones.update` | `PublicacionController@update` |
| DELETE | `/zonas/{pub}` | `publicaciones.destroy` | `PublicacionController@destroy` |
| DELETE | `/zonas/{pub}/imagenes/{img}` | `publicaciones.imagenes.destroy` | `PublicacionController@destroyImagen` |
| POST | `/zonas/{pub}/comentarios` | `comentarios.store` | `ComentarioController@store` |
| DELETE | `/zonas/{pub}/comentarios/{com}` | `comentarios.destroy` | `ComentarioController@destroy` |
| POST | `/zonas/{pub}/like` | `likes.toggle` | `LikeController@toggle` |
| POST | `/zonas/{pub}/reposte` | `repostes.toggle` | `ReposteController@toggle` |
| POST | `/zonas/{pub}/guardar` | `favoritos.toggle` | `FavoritoController@toggle` |
| GET | `/guardados` | `guardados.index` | `FavoritoController@index` |
| GET | `/notificaciones` | `notificaciones.index` | `NotificacionController@index` |
| POST | `/notificaciones/leer` | `notificaciones.read` | `NotificacionController@markAllRead` |
| GET | `/guias` | `guias.index` | `TutorialController@index` |
| GET | `/guias/nuevo` | `tutoriales.create` | `TutorialController@create` |
| POST | `/guias/nuevo` | `tutoriales.store` | `TutorialController@store` |
| GET | `/guias/{tutorial}` | `tutoriales.show` | `TutorialController@show` |
| DELETE | `/guias/{tutorial}` | `tutoriales.destroy` | `TutorialController@destroy` |

> **Nota de implementación:** Las rutas con segmentos estáticos (`/zonas/crear`, `/guias/nuevo`, `/u`) se declaran **antes** de las rutas con parámetros (`/zonas/{pub}`, `/u/{user}`) para evitar que el router las interprete como parámetros.

---

## 11. Políticas de Autorización

**Archivo:** `app/Policies/PublicacionPolicy.php`

Laravel Policies encapsulan las reglas de autorización a nivel de recurso. Se invocan con `$this->authorize()` en controladores y con `@can`/`@canany` en vistas Blade.

```php
class PublicacionPolicy
{
    // Solo el autor puede editar
    public function update(User $user, Publicacion $publicacion): bool
    {
        return $user->id === $publicacion->user_id;
    }

    // El autor O un moderador puede eliminar
    public function delete(User $user, Publicacion $publicacion): bool
    {
        return $user->id === $publicacion->user_id || $user->esModerador();
    }
}
```

**En vistas:**
```blade
{{-- Dropdown visible si puede editar O eliminar --}}
@canany(['update', 'delete'], $publicacion)
    <div class="post-actions">
        @can('update', $publicacion)
            <a href="...">Editar zona</a>    {{-- Solo autor --}}
        @endcan
        @can('delete', $publicacion)
            <button>Eliminar zona</button>    {{-- Autor + Moderador --}}
        @endcan
    </div>
@endcanany
```

---

## 12. Servicios y Jobs

### 12.1 `ImageService`
**Archivo:** `app/Services/ImageService.php`

Encapsula toda la lógica de procesamiento de imágenes usando **Intervention Image v4** con driver GD.

| Método | Uso | Parámetros de compresión |
|---|---|---|
| `store()` | Publicaciones, comentarios, tutoriales | Máx. 1400px ancho, JPEG calidad 82. GIFs sin modificar. |
| `storeAvatar()` | Avatar de perfil | Recorte cuadrado 400×400px, calidad 85. |
| `storeBanner()` | Banner de perfil | Máx. 1500px ancho, calidad 80. |

```php
// Ejemplo: almacenar imagen de publicación
$ruta = ImageService::store($uploadedFile, 'publicaciones');

// Internamente:
$image = $manager->decode($file->getContent()); // Lee bytes del archivo
$image->scaleDown(width: $maxWidth);            // Redimensiona sin ampliar
Storage::disk('public')->put($path, (string) $image->encode(new JpegEncoder($quality)));
```

> **Ahorro de espacio:** Una foto de móvil típica (4032px, ~4 MB) se convierte en un JPEG de ~300-450 KB — una reducción del 85-90%.

### 12.2 `EnviarNotificacion` (Job)
**Archivo:** `app/Jobs/EnviarNotificacion.php`

Job asíncrono que envía notificaciones a través de la cola Redis, evitando bloquear el request HTTP.

```php
class EnviarNotificacion implements ShouldQueue
{
    public int $tries = 3; // Reintentos ante fallo

    public function __construct(
        public readonly int     $destinatario_id,
        public readonly int     $actor_id,
        public readonly string  $tipo,
        public readonly ?int    $publicacion_id = null,
        public readonly ?int    $tutorial_id    = null,
    ) {}

    public function handle(): void
    {
        Notificacion::crear($this->destinatario_id, $this->actor_id, $this->tipo, ...);
    }
}
```

**Dispatch en controladores:**
```php
// Antes (síncrono, bloquea el request):
Notificacion::crear($publicacion->user_id, Auth::id(), 'like', $publicacion->id);

// Ahora (asíncrono, retorna inmediatamente):
EnviarNotificacion::dispatch($publicacion->user_id, Auth::id(), 'like', $publicacion->id);
```

---

## 13. Infraestructura Docker

**Archivo:** `docker-compose.yml`

El sistema se compone de 6 contenedores:

| Contenedor | Imagen | Puerto | Función |
|---|---|---|---|
| `fishspot_app` | Custom (PHP 8.4-FPM) | 9000 (interno) | Ejecuta la aplicación Laravel |
| `fishspot_nginx` | nginx:alpine | 8001→80 | Servidor web y proxy FastCGI |
| `fishspot_db` | mariadb:11.4 | 3307→3306 | Base de datos principal |
| `fishspot_redis` | redis:7-alpine | 6379 (interno) | Caché, sesiones y colas |
| `fishspot_queue` | Custom (PHP 8.4-FPM) | — | Worker de cola de notificaciones |
| `fishspot_phpmyadmin` | phpmyadmin:latest | 8081→80 | Administración de BD (desarrollo) |

### 13.1 Dockerfile (imagen `app` y `queue`)

El Dockerfile compila GD con soporte JPEG y WebP (necesario para Intervention Image):

```dockerfile
RUN apt-get install -y libjpeg62-turbo-dev libwebp-dev ...
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo_mysql mbstring ...
```

### 13.2 Entrypoint (`docker/entrypoint.sh`)

Al arrancar el contenedor `app`, el entrypoint ejecuta automáticamente:
1. Generación de APP_KEY si no existe.
2. Migraciones pendientes (`php artisan migrate --force`).
3. Seeders (`php artisan db:seed --force`).
4. Enlace de almacenamiento público (`storage:link`).
5. Limpieza de caché de configuración y vistas.
6. Inicio de PHP-FPM.

### 13.3 Configuración PHP-FPM (`docker/php/www.conf`)

```ini
pm = dynamic
pm.max_children    = 50   # Máx. workers simultáneos
pm.start_servers   = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests    = 500  # Reinicio del worker cada 500 requests
```

### 13.4 Nginx (`docker/nginx/default.conf`)

```nginx
resolver 127.0.0.11 valid=10s ipv6=off;  # DNS dinámico de Docker

location ~ \.php$ {
    set $upstream app:9000;
    fastcgi_pass $upstream;  # Resolución dinámica: tolera reinicios del contenedor app
}
```

---

## 14. Casos de Uso

### CU-01: Publicar una zona de pesca
**Actor:** Usuario autenticado  
**Precondición:** El usuario ha iniciado sesión.  
**Flujo principal:**
1. El usuario accede a "Publicar zona".
2. Rellena título, descripción, hace clic en el mapa para seleccionar coordenadas GPS.
3. Opcionalmente selecciona temporada, tipo de licencia, etiquetas de especies y sube imágenes.
4. Envía el formulario.
5. El sistema valida los datos y las imágenes (MIME, tamaño).
6. Las imágenes se comprimen y almacenan en `storage/app/public/publicaciones/`.
7. Se crea el registro en BD y se redirige a la vista detalle.
8. La caché del feed se invalida (`Cache::tags('feed')->flush()`).

**Postcondición:** La zona aparece en el feed de otros usuarios y en el mapa.
vale 
---

### CU-02: Dar like a una publicación
**Actor:** Usuario autenticado  
**Flujo principal:**
1. El usuario pulsa el botón de like en una publicación.
2. Se realiza un POST a `/zonas/{pub}/like`.
3. Si ya existe like → se elimina (toggle off).
4. Si no existe → se crea y se despacha `EnviarNotificacion` con tipo `like`.
5. El worker de cola procesa el job y crea la notificación en BD.
6. Se redirige de vuelta a la página anterior.

---

### CU-03: Seguir a un usuario
**Actor:** Usuario autenticado  
**Flujo principal:**
1. El usuario visita el perfil de otro usuario.
2. Pulsa "Seguir".
3. Se realiza POST a `/u/{user}/seguir`.
4. Si ya sigue → se desvincula (unfollow).
5. Si no sigue → se vincula y se despacha notificación tipo `seguir`.
6. El perfil del seguido aparece en el feed prioritario del seguidor.

---

### CU-04: Comentar con respuesta anidada
**Actor:** Usuario autenticado  
**Flujo principal:**
1. El usuario accede a la vista detalle de una zona.
2. Escribe un comentario en el compositor. Puede adjuntar hasta 4 imágenes.
3. Si pulsa "Responder" en un comentario existente, el `parent_id` se incluye en el formulario.
4. Se realiza POST a `/zonas/{pub}/comentarios`.
5. Si hay `parent_id` → notificación al autor del comentario padre. Si no → notificación al autor de la publicación.
6. Las imágenes se comprimen y almacenan.

---

### CU-05: Moderar contenido
**Actor:** Usuario con `rol = moderador`  
**Flujo principal:**
1. El moderador navega a cualquier publicación.
2. Ve el menú de tres puntos (⋮) que incluye "Eliminar zona" (sin "Editar zona").
3. Confirma la eliminación.
4. El sistema verifica en `PublicacionPolicy::delete()` que el usuario es moderador.
5. Se eliminan las imágenes del disco y el registro de BD.
6. Se invalida la caché del feed.

---

### CU-06: Publicar un tutorial
**Actor:** Usuario autenticado  
**Flujo principal:**
1. El usuario accede a "Equipos y Guías" → "Nuevo tutorial".
2. Rellena título, categoría e imagen de cabecera.
3. Al guardar, el sistema notifica a todos los seguidores del autor (un job por seguidor, procesados en cola).

---

### CU-07: Filtrar zonas en el mapa por etiqueta, temporada o licencia
**Actor:** Usuario autenticado  
**Precondición:** El usuario ha iniciado sesión y se encuentra en el feed principal.  
**Flujo principal:**
1. El usuario abre el panel lateral de filtros desde el feed (`/`).
2. Selecciona una o varias etiquetas de especie (p. ej. "Carpa"), una temporada y/o un tipo de licencia.
3. El sistema realiza una llamada AJAX a `GET /zonas/filtrar` con los parámetros seleccionados.
4. El mapa elimina los marcadores actuales y renderiza únicamente los que coinciden con el filtro.
5. Las tarjetas del feed se actualizan con los resultados filtrados.
6. Si ninguna zona cumple los criterios, el mapa muestra el mensaje _"Ninguna zona coincide"_ y sugiere ampliar los filtros.
7. El estado de los filtros queda codificado en la URL como query params para compartir la búsqueda.
8. El usuario puede eliminar todos los filtros con un botón "Limpiar" que restaura la vista completa sin recargar la página.

---

### CU-08: Buscar zonas de pesca cercanas a una ubicación
**Actor:** Usuario autenticado  
**Precondición:** El usuario ha iniciado sesión.  
**Flujo principal:**
1. El usuario accede a la búsqueda (`/buscar`) o pulsa "Cerca de mí" en el mapa.
2. El navegador solicita permiso de geolocalización mediante `navigator.geolocation`.
3. Si se concede, las coordenadas del dispositivo se usan como centro de búsqueda. Si se deniega, el usuario hace clic en el mapa para fijar el punto manualmente.
4. El usuario selecciona un radio predefinido: 5 km, 10 km, 25 km o 50 km.
5. El sistema ejecuta una query con la fórmula de Haversine sobre `(latitud, longitud)` de la tabla `publicaciones`, usando el scope `scopeNearCoordinates`.
6. Los resultados se devuelven ordenados por distancia ascendente, mostrando la distancia calculada en cada tarjeta.
7. Si no hay resultados, el sistema sugiere ampliar al siguiente radio disponible.
8. La búsqueda puede combinarse con los filtros de etiqueta, temporada y licencia del CU-07.

---

## 15. Flujos de Usuario

### 15.1 Flujo de registro y primera visita

```
[Landing page / welcome.blade.php]
           │
           │ "Registrarse"
           ▼
[Formulario de registro]
  name + email + password
           │
           │ POST /register
           ▼
[Creación de usuario en BD]
  rol = 'user' (por defecto)
           │
           ▼
[Redirección al feed / index]
  Feed vacío → muestra todas las publicaciones
  sin priorización (sin follows todavía)
```

### 15.2 Flujo de feed con priorización

```
[Usuario autenticado visita /]
           │
           ▼
   ¿Tiene usuarios seguidos?
           │
    ┌──────┴──────┐
   SÍ            NO
    │              │
    ▼              ▼
[Calcular tier-1 IDs]   [Query directa]
  ¿En caché Redis?       últimas 20 zonas
    │                    (excluyendo propias)
  ┌─┴──┐
HIT   MISS
  │     │
  │   [3 queries DB]
  │   posts de seguidos
  │   + likes de seguidos
  │   + repostes de seguidos
  │   → guardar en Redis (120s)
  │     │
  └─────┘
    │
    ▼
[1 query paginada con CASE WHEN]
  Tier-1 primero, resto después
    │
    ▼
[Vista feed con tarjetas compactas]
```

### 15.3 Flujo completo de notificación asíncrona

```
[Usuario A da like a publicación de B]
           │
           │ POST /zonas/{pub}/like
           ▼
[LikeController::toggle()]
  Crea registro en tabla likes
           │
           │ EnviarNotificacion::dispatch(...)
           ▼
[Job serializado → Redis queue]
  Retorno HTTP inmediato al usuario A
           │
           │ (en paralelo, en fishspot_queue)
           ▼
[Queue worker procesa job]
  Notificacion::crear(B.id, A.id, 'like', pub.id)
  Inserta registro en tabla notificaciones
           │
           ▼
[Próxima visita de B al feed]
  Badge de notificación en sidebar
  Al visitar /notificaciones → ve "A le ha gustado tu zona"
```

### 15.4 Flujo de subida y compresión de imágenes

```
[Usuario sube foto de móvil]
  Original: 4032×3024px, ~4 MB JPEG
           │
           ▼
[Validación: MIME, tamaño, extensión]
           │
           ├─ GIF? → almacenar sin modificar
           │
           ▼
[ImageService::store()]
  $manager->decode(bytes)
  ->scaleDown(width: 1400)       → 1400×1050px
  ->encode(new JpegEncoder(82))  → compresión
           │
           ▼
[Storage::disk('public')->put()]
  Ruta: publicaciones/{uuid}.jpg
  Tamaño resultante: ~300-450 KB
  Ahorro: ~85-90%
```

---

## 16. Sistema de Roles

El sistema implementa dos roles mediante la columna `rol` en la tabla `users`:

| Rol | Valor | Capacidades |
|---|---|---|
| Usuario | `user` | Crear, editar y eliminar sus propias publicaciones y comentarios. |
| Moderador | `moderador` | Todo lo anterior + eliminar publicaciones de cualquier usuario. |

**Implementación:**

```
Columna users.rol ENUM('user', 'moderador') DEFAULT 'user'
         │
         ▼
User::esModerador() → bool
         │
         ▼
PublicacionPolicy::delete() → user.id == pub.user_id OR esModerador()
         │
         ├─ Controlador: $this->authorize('delete', $publicacion)
         └─ Vista: @canany(['update','delete'], $publicacion)
```

**Credenciales por defecto (seeder):**

| Rol | Email | Contraseña |
|---|---|---|
| Moderador | `mod@fishspot.local` | `password` |
| Usuario | `carlos@fishspot.local` | `password` |
| Usuario | `maria@fishspot.local` | `password` |
| (17 más) | `{nombre}@fishspot.local` | `password` |

---

## 17. Optimización y Rendimiento

### 17.1 Caché Redis del feed

El cálculo de IDs prioritarios del feed implica 3 queries pesadas sobre tablas potencialmente grandes (likes, repostes, publicaciones). Se cachea el resultado como string en Redis:

```
Clave:  feed.tier1.{user_id}
Valor:  "12,47,83,91,..." (IDs separados por coma)
TTL:    120 segundos
Tag:    'feed' (permite invalidación masiva con Cache::tags('feed')->flush())
```

> **Importante:** Se cachea solo el string de IDs, **nunca el objeto paginador**. Almacenar objetos Eloquent serializados en Redis provoca errores de deserialización.

**Invalidación:** Cuando un usuario publica o elimina una zona, se llama a `Cache::tags('feed')->flush()` para que el próximo request recalcule la lista con los datos actualizados.

### 17.2 PHP-FPM

Configuración en `docker/php/www.conf`:
- De 5 workers (por defecto) a **50 workers máximo**.
- `pm.max_requests = 500`: reinicia el worker cada 500 requests para prevenir fugas de memoria.

### 17.3 Sesiones en Redis

Las sesiones de usuario se almacenan en Redis (`SESSION_DRIVER=redis`) en lugar del sistema de ficheros, reduciendo latencia de I/O y facilitando el escalado horizontal.

### 17.4 Notificaciones asíncronas

Antes de implementar la cola, crear un tutorial con 50 seguidores bloqueaba el request ~50 inserts de BD en serie. Con el sistema de colas, el request finaliza en milisegundos y el worker procesa las notificaciones en paralelo.

---

*Documentación generada para FishSpot España · TFG · Curso 2025-2026*
