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
│       └── ImageService.php        # Redimensionado y almacenamiento de imágenes
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
