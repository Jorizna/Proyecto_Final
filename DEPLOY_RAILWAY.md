# Guía de Despliegue en Railway — FishSpot España

> Laravel 13 · PHP 8.4 · MySQL · Redis · Docker

---

## Índice

1. [Requisitos previos](#1-requisitos-previos)
2. [Arquitectura en Railway](#2-arquitectura-en-railway)
3. [Preparar el repositorio en GitHub](#3-preparar-el-repositorio-en-github)
4. [Crear el proyecto en Railway](#4-crear-el-proyecto-en-railway)
5. [Añadir MySQL](#5-añadir-mysql)
6. [Añadir Redis](#6-añadir-redis)
7. [Configurar variables de entorno](#7-configurar-variables-de-entorno)
8. [Primer despliegue](#8-primer-despliegue)
9. [Almacenamiento persistente de imágenes](#9-almacenamiento-persistente-de-imágenes)
10. [Añadir el Queue Worker](#10-añadir-el-queue-worker)
11. [Verificar que todo funciona](#11-verificar-que-todo-funciona)
12. [Dominio personalizado (opcional)](#12-dominio-personalizado-opcional)
13. [Operaciones de mantenimiento](#13-operaciones-de-mantenimiento)
14. [Solución de problemas frecuentes](#14-solución-de-problemas-frecuentes)

---

## 1. Requisitos previos

Antes de comenzar necesitas tener listo lo siguiente:

- **Cuenta en GitHub** con el proyecto subido a un repositorio (público o privado).
- **Cuenta en Railway** — créala en [railway.app](https://railway.app) con tu cuenta de GitHub (es gratis, no requiere tarjeta para el plan Starter).
- **Entorno local funcionando** — el proyecto debe arrancar correctamente con `docker compose up -d` antes de desplegarlo.
- **APP_KEY generada** — ejecuta esto en tu entorno local para obtener la clave de cifrado:

```bash
docker compose exec app php artisan key:generate --show
```

Guarda el resultado (algo como `base64:abc123...`), lo necesitarás más adelante.

---

## 2. Arquitectura en Railway

Railway no ejecuta Docker Compose directamente. En lugar de los 5 servicios locales, el proyecto se despliega como 3 servicios en Railway:

| Servicio local | Equivalente en Railway |
|---|---|
| `fishspot_app` (PHP-FPM) | **Web service** — `Dockerfile.railway` incluye PHP-FPM + Nginx en el mismo contenedor gestionados por Supervisor |
| `fishspot_nginx` (Nginx) | Incluido dentro del Web service |
| `fishspot_db` (MariaDB) | **MySQL plugin** — base de datos gestionada por Railway |
| `fishspot_redis` (Redis) | **Redis plugin** — Redis gestionado por Railway |
| `fishspot_queue` (Worker) | **Queue service** — mismo repo, comando `php artisan queue:work` |

```
Internet
    │
    ▼
Railway (URL pública + HTTPS automático)
    │
    ├── Web service (nginx → php-fpm, puerto 80)
    │       └── Dockerfile.railway
    │
    ├── Queue service (php artisan queue:work)
    │       └── Dockerfile.railway + comando personalizado
    │
    ├── MySQL plugin (base de datos gestionada)
    │
    └── Redis plugin (caché + sesiones + colas)
```

---

## 3. Preparar el repositorio en GitHub

Si el proyecto aún no está en GitHub, créalo ahora. Asegúrate de que estos archivos están presentes y commiteados:

```
Dockerfile.railway              ← imagen de producción (nginx + php-fpm)
railway.toml                    ← configuración del proyecto
docker/nginx/railway.conf       ← configuración nginx sin resolver Docker
docker/supervisor/supervisord.conf  ← supervisor para nginx + php-fpm
docker/entrypoint.railway.sh    ← entrypoint de producción
railway.env.example             ← plantilla de variables (referencia)
```

Verifica que el `.gitignore` incluye `.env` para no subir credenciales:

```bash
git status
# El archivo .env NO debe aparecer como nuevo o modificado
```

Si el proyecto no está en GitHub todavía:

```bash
git init
git add .
git commit -m "Configuración Railway"
git remote add origin https://github.com/TU_USUARIO/TU_REPO.git
git push -u origin master
```

---

## 4. Crear el proyecto en Railway

### 4.1 Nuevo proyecto desde GitHub

1. Entra en [railway.app](https://railway.app) y haz clic en **New Project**.
2. Selecciona **Deploy from GitHub repo**.
3. Si es la primera vez, autoriza Railway a acceder a tu cuenta de GitHub.
4. Busca y selecciona tu repositorio.

Railway detectará automáticamente el archivo `railway.toml` y usará `Dockerfile.railway` para construir la imagen.

### 4.2 Lo que verás en pantalla

Railway crea automáticamente el primer servicio (el web) e inicia un build. **Este primer build fallará** porque aún no tiene la base de datos ni las variables de entorno configuradas. Es normal — continúa con los siguientes pasos sin esperar a que termine.

---

## 5. Añadir MySQL

### 5.1 Crear el servicio de base de datos

1. Dentro de tu proyecto en Railway, haz clic en **+ Add Service**.
2. Selecciona **Database → MySQL**.
3. Railway aprovisiona el servidor MySQL automáticamente (tarda ~1 minuto).

### 5.2 Anotar el nombre del servicio

Una vez creado, haz clic en el servicio MySQL → pestaña **Variables**. Verás variables como:

```
MYSQL_HOST      →  containers-us-west-xxx.railway.internal
MYSQL_PORT      →  3306
MYSQL_DATABASE  →  railway
MYSQL_USER      →  root
MYSQL_PASSWORD  →  xxxxxxxxxx
MYSQL_URL       →  mysql://root:xxx@host:3306/railway
```

No necesitas copiar estos valores manualmente. Railway permite referenciarlos directamente desde el servicio web con la sintaxis `${{MySQL.NOMBRE_VARIABLE}}`.

---

## 6. Añadir Redis

### 6.1 Crear el servicio Redis

1. Haz clic en **+ Add Service → Database → Redis**.
2. Railway aprovisiona Redis automáticamente.

### 6.2 Variable disponible

En el servicio Redis → **Variables** encontrarás:

```
REDIS_URL  →  redis://:password@host:port
```

Esta variable se referenciará como `${{Redis.REDIS_URL}}` desde el servicio web.

---

## 7. Configurar variables de entorno

### 7.1 Abrir las variables del servicio web

Haz clic en tu **servicio web** (el creado desde GitHub) → pestaña **Variables**.

### 7.2 Añadir las variables

Añade cada variable a continuación. Puedes usar el botón **Raw Editor** para pegarlas todas de golpe:

```
APP_NAME=FishSpot España
APP_ENV=production
APP_DEBUG=false
APP_URL=https://TU-APP.up.railway.app
APP_KEY=base64:PEGA_AQUI_TU_CLAVE_GENERADA_EN_LOCAL

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

REDIS_URL=${{Redis.REDIS_URL}}
REDIS_CLIENT=predis

SESSION_DRIVER=redis
SESSION_LIFETIME=120
CACHE_STORE=redis
QUEUE_CONNECTION=redis

FILESYSTEM_DISK=public
MAIL_MAILER=log

LOG_CHANNEL=stack
LOG_LEVEL=error
```

> **Importante:** Sustituye `TU-APP.up.railway.app` por la URL real que Railway asigna a tu servicio (la encontrarás en **Settings → Domains**). Y no olvides pegar tu `APP_KEY` real.

### 7.3 Cómo funcionan las referencias `${{...}}`

Cuando escribes `${{MySQL.MYSQL_HOST}}`, Railway sustituye automáticamente ese valor por la variable `MYSQL_HOST` del servicio llamado `MySQL`. Esto significa que aunque la contraseña de la BD cambie, el servicio web siempre tendrá el valor actualizado sin tocar nada.

---

## 8. Primer despliegue

### 8.1 Desencadenar el build

Una vez guardadas las variables, Railway lanza automáticamente un nuevo build. Si no lo hace, ve a la pestaña **Deployments** y haz clic en **Deploy**.

### 8.2 Seguir los logs del build

Haz clic en el deployment en curso → pestaña **Build Logs**. El proceso tiene estas fases:

```
[1/8] Instalando dependencias del sistema (nginx, supervisor...)
[2/8] Instalando extensiones PHP
[3/8] Instalando dependencias Composer
[4/8] Copiando código de la aplicación
[5/8] Generando autoloader optimizado
[6/8] Configurando nginx y supervisor
[7/8] Ajustando permisos
[8/8] Imagen construida correctamente
```

### 8.3 Seguir los logs de arranque

Una vez construida la imagen, Railway arranca el contenedor. En **Deploy Logs** verás:

```
==> Ajustando permisos de storage...
==> Limpiando caché de arranque anterior...
==> Comprobando APP_KEY...
    APP_KEY ya configurada.
==> Ejecutando migraciones...
    Running migrations...
    ✓ 2024_01_01_100001_create_users_table
    ✓ 2024_01_01_100002_create_publicaciones_table
    ...
==> Verificando si la BD ya está inicializada...
==> BD vacía, ejecutando seeders iniciales...
==> Enlazando almacenamiento público...
==> Optimizando para producción...
==> Iniciando nginx + php-fpm vía supervisor...
```

Si ves todo esto sin errores en rojo, el despliegue ha sido exitoso.

### 8.4 Obtener la URL pública

Ve a **Settings → Domains**. Railway habrá generado una URL como:

```
https://fishspot-production-xxxx.up.railway.app
```

Haz clic en ella — deberías ver la landing page de FishSpot.

---

## 9. Almacenamiento persistente de imágenes

Sin esta configuración, las imágenes subidas por los usuarios **se perderán en cada redeploy** porque el sistema de ficheros del contenedor es efímero.

### 9.1 Crear un volumen en Railway

1. En tu servicio web → pestaña **Volumes**.
2. Haz clic en **Add Volume**.
3. Configura:
   - **Mount Path:** `/var/www/storage/app/public`
4. Guarda. Railway reiniciará el contenedor con el volumen montado.

### 9.2 Por qué esta ruta

Laravel almacena los ficheros subidos en `storage/app/public/`. El entrypoint ejecuta `php artisan storage:link`, que crea el enlace simbólico `public/storage → storage/app/public`. Al montar el volumen en esa ruta, las imágenes quedan en disco persistente independientemente de los redeploys.

---

## 10. Añadir el Queue Worker

El queue worker procesa las notificaciones asíncronas. Sin él, los likes, comentarios y follows no generan notificaciones (la acción funciona, pero la notificación nunca se entrega).

### 10.1 Crear el servicio

1. En tu proyecto Railway → **+ Add Service → GitHub Repo**.
2. Selecciona el mismo repositorio.

### 10.2 Configurar el comando de inicio

Railway intentará construir y arrancar como un servicio web. Necesitamos cambiar eso:

1. Haz clic en el nuevo servicio → **Settings**.
2. En **Deploy → Start Command**, introduce:
   ```
   php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
   ```
3. En **Build → Dockerfile Path**, introduce:
   ```
   Dockerfile.railway
   ```

### 10.3 Copiar las variables de entorno

El queue worker necesita las mismas variables que el servicio web para conectarse a la BD y Redis.

1. En el servicio queue → pestaña **Variables**.
2. Haz clic en **Add Reference** o pega manualmente las mismas variables del paso 7.

La forma más rápida: en la pestaña Variables del servicio queue, usa el botón **Share Variables** y referencia el servicio web, o copia el bloque del Raw Editor del servicio web y pégalo aquí.

### 10.4 Verificar que el worker está activo

En **Deploy Logs** del servicio queue deberías ver:

```
[2024-xx-xx] Processing: App\Jobs\EnviarNotificacion
[2024-xx-xx] Processed: App\Jobs\EnviarNotificacion
```

Si ves `No config files found` o similar, revisa que el Dockerfile Path está correctamente configurado.

---

## 11. Verificar que todo funciona

Accede a tu URL pública y realiza estas comprobaciones en orden:

| Acción | Resultado esperado |
|---|---|
| Visitar la URL raíz | Landing page o feed (si estás logueado) |
| Registrar una cuenta nueva | Redirección al feed |
| Iniciar sesión con `carlos@fishspot.local` / `password` | Acceso correcto al feed |
| Ver el mapa | Marcadores de zonas visibles |
| Dar like a una publicación | Contador incrementa |
| Subir una imagen en una nueva publicación | Imagen visible después de guardar |
| Visitar `/notificaciones` | Las notificaciones aparecen (puede tardar unos segundos si el worker acaba de arrancar) |
| Cerrar sesión y volver a entrar | La sesión persiste correctamente (Redis) |
| Hacer redeploy desde Railway | Las imágenes subidas siguen existiendo (volumen) |

---

## 12. Dominio personalizado (opcional)

Railway proporciona una URL pública gratuita. Si quieres usar un dominio propio (por ejemplo `fishspot.es`):

### 12.1 Añadir el dominio en Railway

1. Servicio web → **Settings → Domains → Add Custom Domain**.
2. Escribe tu dominio (ej. `fishspot.es` o `www.fishspot.es`).
3. Railway te mostrará un registro DNS de tipo **CNAME** con un valor como:
   ```
   CNAME → fishspot-production-xxxx.up.railway.app
   ```

### 12.2 Configurar el DNS en tu proveedor de dominio

En el panel de tu registrador de dominios (Namecheap, Porkbun, GoDaddy...):

1. Accede a la gestión de DNS de tu dominio.
2. Añade o edita el registro:
   - **Tipo:** CNAME
   - **Host:** `www` (o `@` para el dominio raíz)
   - **Valor:** el que te dio Railway
3. Guarda. La propagación tarda entre 5 minutos y 24 horas.

### 12.3 Actualizar APP_URL

Una vez propagado el DNS, actualiza la variable en Railway:
```
APP_URL=https://fishspot.es
```

Railway gestiona el certificado HTTPS (Let's Encrypt) automáticamente.

---

## 13. Operaciones de mantenimiento

### Resetear la base de datos y volver a seedear

Desde Railway → servicio web → **Settings → Deploy → Execute Command**:

```bash
php artisan migrate:fresh --seed --force
```

> ⚠️ Esto borra todos los datos. Úsalo solo en entornos de prueba.

### Ejecutar un comando artisan puntual

Railway permite ejecutar comandos en un contenedor activo. Ve a tu servicio → **Settings → Execute Command** (o usa la Railway CLI):

```bash
# Con Railway CLI instalada:
railway run php artisan cache:clear
railway run php artisan queue:restart
railway run php artisan migrate --force
```

### Instalar la Railway CLI

```bash
# macOS / Linux
curl -fsSL https://railway.app/install.sh | sh

# Windows (PowerShell)
iwr -useb https://railway.app/install.ps1 | iex
```

Autenticarse:
```bash
railway login
railway link   # dentro del directorio del proyecto, vincula con tu proyecto Railway
```

### Ver logs en tiempo real

```bash
# Logs del servicio web
railway logs

# Logs del queue worker (especificando el servicio)
railway logs --service queue
```

### Reiniciar el queue worker tras un cambio de código

El worker carga el código en memoria al arrancar. Después de un redeploy, Railway reinicia el contenedor automáticamente. Si necesitas reiniciarlo manualmente:

```bash
railway run php artisan queue:restart
```

---

## 14. Solución de problemas frecuentes

### El build falla con "Class not found"

**Causa:** El autoloader no se generó correctamente.  
**Solución:** Verifica que `composer.lock` está commiteado en el repositorio. Railway no ejecuta `composer install` sin este fichero.

---

### La app muestra "500 Server Error" o pantalla en blanco

**Causa:** `APP_DEBUG=false` oculta los errores. El error real está en los logs.  
**Solución:** Mira los Deploy Logs en Railway. Si necesitas depurar temporalmente, cambia `APP_DEBUG=true` en las variables (recuerda revertirlo).

```bash
railway logs --tail 100
```

---

### "SQLSTATE: Access denied" o "Connection refused" a la BD

**Causas frecuentes:**
1. Las variables `DB_*` no están referenciando correctamente el servicio MySQL.
2. El servicio MySQL no está en el mismo proyecto Railway.

**Comprobación:** En Variables del servicio web, haz clic en cada variable `${{MySQL.xxx}}` — Railway debe mostrar el valor resuelto al lado. Si no lo muestra, el servicio MySQL no está vinculado.

---

### Las imágenes subidas desaparecen tras un redeploy

**Causa:** El volumen de Railway no está configurado o no está montado en la ruta correcta.  
**Solución:** Revisa el paso 9. El Mount Path debe ser exactamente `/var/www/storage/app/public`.

---

### Las notificaciones no llegan

**Causa:** El queue worker no está corriendo.  
**Comprobación:** En Railway, el servicio queue debe estar en estado **Active** (verde). Si está en **Crashed**, revisa sus Deploy Logs.

**Soluciones comunes:**
- El Start Command no está configurado → revisar paso 10.2.
- Las variables de entorno no están copiadas → revisar paso 10.3.
- El worker procesó todos los jobs y se detuvo → es normal con `--max-time=3600`, Railway lo reiniciará automáticamente.

---

### "The stream or file .../laravel.log could not be opened"

**Causa:** El directorio `storage/logs` no tiene permisos de escritura.  
**Solución:** El entrypoint de producción ejecuta `chmod -R 775 /var/www/storage` al arrancar. Si persiste, ejecuta manualmente:

```bash
railway run chmod -R 775 /var/www/storage
```

---

### El mapa no carga (pantalla en blanco donde debería estar Leaflet)

**Causa:** CSP o problema con los CDN de Leaflet.  
**Comprobación:** Abre las DevTools del navegador (F12) → pestaña Console. Si hay errores de red, verifica que la URL de Railway usa HTTPS (los CDN de Leaflet requieren HTTPS).

---

*Guía de despliegue para FishSpot España · TFG · Curso 2025-2026*
