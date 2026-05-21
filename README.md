# 🎣 FishSpot Aragón

Plataforma web colaborativa de zonas de pesca recreativa en Aragón. Los usuarios pueden publicar zonas de pesca con ubicación en el mapa, imágenes, comentarios y valoraciones. Incluye datos meteorológicos de AEMET y detección de masas de agua cercanas mediante OpenStreetMap.

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 13 (PHP 8.3) |
| Base de datos | MariaDB 11.4 |
| Entorno | Docker + Docker Compose |
| Mapa | Leaflet.js + OpenStreetMap |
| Plantillas | Blade |
| Estilos | CSS propio (sin framework frontend) |

---

## Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y en ejecución
- Git

---

## Primera ejecución

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd proyecto_final
```

### 2. Copiar el archivo de entorno

```bash
cp .env.example .env
```

### 3. Construir e iniciar los contenedores

```bash
docker compose up -d --build
```

Este comando construye la imagen PHP, descarga las imágenes de MariaDB y phpMyAdmin, e inicia los tres contenedores. La primera vez tarda entre 3 y 5 minutos.

Al arrancar, el contenedor de la aplicación ejecuta automáticamente:
- Generación de `APP_KEY`
- Migraciones de base de datos
- Seeders (usuario demo + 14 etiquetas de especies)
- Enlace de almacenamiento público (`storage:link`)

### 4. Verificar que todo está en marcha

```bash
docker compose ps
```

Todos los servicios deben aparecer como `running`.

### 5. Abrir la aplicación

| Servicio | URL |
|---|---|
| Aplicación | http://localho# Role: Expert Frontend & UI Designer
# Task: Implement Compact Floating Map Filter Suite by Fish Type

Please implement a lightweight, compact filtering system integrated directly into the top area of the Map component within the "Explorar" section.

---

## 1. Minimalist Floating Layout (Top of the Map)
* **The UI Layout:** Position the search and filter bar as a absolute-positioned floating element **inside the upper border of the map container** (e.g., `absolute top-4 left-4 z-[400]`), or right above it with minimal padding (`py-2`). It must be low-profile and take up very little vertical space.
* **Compact Controls:** Design a single, cohesive horizontal bar containing:
    - A small search input box.
    - A clean filter icon button (e.g., a "Filtrar" gear or funnel icon).

## 2. Dropdown Filter Menu Logic (Fish Type)
* **The Interaction:** Clicking the filter button must toggle a small, floating dropdown menu directly beneath the bar (without pushing the map downwards).
* **"Tipo de Pez" (Fish Type) Filter Selection:** - Inside this small dropdown grid, include a clean picker for **"Tipo de pez"** (e.g., Carpa, Trucha, Black Bass).
* **Map Update Logic:**
    - Selecting a fish type must instantly filter the active markers on the map using state management.
    - **ONLY show zones on the map** that match the chosen species.
    - If the filter is cleared, instantly restore all public fishing zone markers.st:8001 |
| phpMyAdmin | http://localhost:8081 |

**Credenciales del usuario de prueba:**
- Email: `demo@fishspot.local`
- Contraseña: `password`

---

## Ejecución normal (arranque diario)

Una vez construida la imagen, para iniciar y detener el entorno:

```bash
# Arrancar
docker compose up -d

# Detener (conserva la base de datos)
docker compose down

# Ver logs en tiempo real
docker compose logs -f app
```

---

## Comandos útiles de desarrollo

```bash
# Acceder al contenedor de la aplicación
docker compose exec app bash

# Ejecutar comandos Artisan
docker compose exec app php artisan migrate:status
docker compose exec app php artisan db:seed
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:list

# Consola interactiva de Laravel (Tinker)
docker compose exec app php artisan tinker
```

---

## Configuración de AEMET (meteorología)

Para mostrar datos meteorológicos en la vista de detalle de cada zona:

1. Solicitar clave gratuita en https://opendata.aemet.es/centrodedescargas/altaUsuario
2. Añadir la clave en el archivo `.env`:

```env
AEMET_API_KEY=tu_clave_aqui
```

3. Reiniciar el contenedor de aplicación:

```bash
docker compose restart app
```

Los datos se cachean 30 minutos para no superar el límite de llamadas de la API gratuita.

---

## Estructura del proyecto

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/                   # Autenticación (registro, login, etc.)
│   │   ├── PublicacionController.php
│   │   ├── ComentarioController.php
│   │   ├── ValoracionController.php
│   │   ├── FavoritoController.php
│   │   └── PerfilController.php
│   ├── Models/                     # Eloquent: User, Publicacion, Comentario...
│   ├── Policies/                   # Autorización de publicaciones
│   └── Services/
│       ├── AemetService.php        # API meteorológica AEMET
│       └── OverpassService.php     # Masas de agua (OpenStreetMap)
├── database/
│   ├── migrations/                 # 8 tablas del dominio
│   └── seeders/                    # Usuario demo + 14 especies de pesca
├── resources/views/
│   ├── layouts/app.blade.php       # Layout principal
│   ├── publicaciones/              # index, show, create, edit
│   ├── perfil/                     # show, edit
│   └── auth/                       # login, register, etc.
├── public/css/app.css              # Estilos propios
├── docker/
│   ├── nginx/default.conf
│   ├── php/local.ini
│   ├── mysql/my.cnf
│   └── entrypoint.sh
└── docker-compose.yml
```

---

## Puertos utilizados

| Puerto | Servicio |
|---|---|
| 8001 | Aplicación (Nginx → PHP-FPM) |
| 8081 | phpMyAdmin |
| 3307 | MariaDB (acceso externo) |

> La comunicación interna entre contenedores usa siempre el puerto 3306.

---

## Restablecer la base de datos

```bash
# Eliminar contenedores y volumen de datos
docker compose down -v

# Volver a levantar (recrea la BD desde cero)
docker compose up -d
```
