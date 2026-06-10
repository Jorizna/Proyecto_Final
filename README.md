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
