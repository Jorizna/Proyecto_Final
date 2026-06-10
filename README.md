# FishSpot Aragón

Red social de pesca para Aragón. Los usuarios comparten zonas de pesca geolocalizadas, interactúan con posts estilo Twitter (likes, repostes, comentarios con imágenes) y publican guías de equipamiento.

## Stack

- **Backend:** Laravel 13 / PHP 8.4
- **Base de datos:** MariaDB 11.4
- **Infraestructura:** Docker + Docker Compose
- **Frontend:** Blade, CSS propio (`public/css/app.css`), Leaflet.js (mapa interactivo)

## Requisitos

- Docker Desktop
- Docker Compose

## Puesta en marcha

```bash
# Primera vez (construye imágenes, ejecuta migraciones y seeders)
docker compose up -d --build

# Arrancar
docker compose up -d

# Parar
docker compose down
```

| Servicio     | URL                        |
|--------------|----------------------------|
| Aplicación   | http://localhost:8001       |
| phpMyAdmin   | http://localhost:8081       |

**Usuario demo:** `demo@fishspot.local` / `password`

## Estructura del proyecto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   └── RegisteredUserController.php
│   │   │   ├── ComentarioController.php
│   │   │   ├── FavoritoController.php
│   │   │   ├── FollowController.php
│   │   │   ├── LikeController.php
│   │   │   ├── NotificacionController.php
│   │   │   ├── PerfilController.php
│   │   │   ├── PublicacionController.php
│   │   │   ├── ReposteController.php
│   │   │   ├── TutorialController.php
│   │   │   └── UsuarioController.php
│   │   └── Requests/Auth/LoginRequest.php
│   ├── Models/
│   │   ├── Comentario.php
│   │   ├── Etiqueta.php
│   │   ├── Favorito.php
│   │   ├── Follow.php
│   │   ├── Imagen.php
│   │   ├── ImagenComentario.php
│   │   ├── Like.php
│   │   ├── Notificacion.php
│   │   ├── Publicacion.php
│   │   ├── Reposte.php
│   │   ├── Tutorial.php
│   │   └── User.php
│   ├── Policies/
│   │   └── PublicacionPolicy.php
│   └── Services/
│       └── ImageService.php        # Redimensionado y almacenamiento de imágenes
├── database/
│   ├── migrations/                 # 20 migraciones (ver detalle abajo)
│   └── seeders/                    # Usuario demo + especies de pesca
├── resources/
│   ├── css/app.css
│   ├── images/
│   └── views/
│       ├── auth/                   # login.blade.php, register.blade.php
│       ├── guardados/              # index.blade.php
│       ├── guias/                  # index, show, create
│       ├── layouts/app.blade.php   # Layout principal
│       ├── legal/privacidad.blade.php
│       ├── notificaciones/index.blade.php
│       ├── pagination/fishspot.blade.php
│       ├── perfil/edit.blade.php
│       ├── publicaciones/          # index, show, create, edit, buscar, _reply
│       ├── usuarios/               # show.blade.php, buscar.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── web.php
│   └── auth.php
├── docker/
│   ├── nginx/default.conf
│   ├── php/local.ini
│   ├── mysql/my.cnf
│   └── entrypoint.sh
├── docker-compose.yml
├── Dockerfile
└── Dockerfile.railway
```

## Rutas principales

| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/` | Mapa + feed de zonas (splash para invitados) |
| GET | `/buscar` | Buscador de publicaciones |
| GET | `/u` | Buscador de usuarios |
| GET | `/u/{user}` | Perfil público de un usuario |
| POST | `/u/{user}/seguir` | Seguir / dejar de seguir |
| GET | `/perfil` | Perfil propio |
| GET | `/perfil/editar` | Editar perfil (avatar, bio, banner) |
| GET | `/zonas/crear` | Nueva zona de pesca |
| GET | `/zonas/{publicacion}` | Detalle de zona (post + replies) |
| POST | `/zonas/{pub}/like` | Like / unlike |
| POST | `/zonas/{pub}/reposte` | Reposte / desreposte |
| POST | `/zonas/{pub}/guardar` | Guardar / quitar de favoritos |
| GET | `/guardados` | Zonas guardadas |
| GET | `/notificaciones` | Centro de notificaciones |
| GET | `/guias` | Hub de guías de equipamiento |
| GET | `/guias/{tutorial}` | Detalle de guía |
| GET | `/privacidad` | Política de privacidad (pública) |

## Tablas principales

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios (avatar, bio, banner, rol) |
| `publicaciones` | Zonas de pesca (coords, especie, temporada, licencia) |
| `comentarios` | Respuestas anidadas a publicaciones |
| `imagenes` | Fotos de publicaciones |
| `imagenes_comentario` | Fotos de comentarios (múltiples por respuesta) |
| `likes` | Like por usuario/publicación |
| `repostes` | Reposte por usuario/publicación |
| `favoritos` | Publicaciones guardadas |
| `follows` | Relación follower/following |
| `etiquetas` | Tags de especie (N:M con publicaciones) |
| `tutoriales` | Guías de equipamiento |
| `notificaciones` | Eventos: like, reposte, comentario, follow |

## Comandos útiles

```bash
# Ejecutar migraciones manualmente
docker compose exec app php artisan migrate

# Ver logs de la app
docker compose logs app

# Entrar al contenedor
docker compose exec app bash
```
