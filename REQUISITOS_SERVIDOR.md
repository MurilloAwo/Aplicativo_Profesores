# Requisitos del servidor de destino

Aplicativo: Laravel 10 (monolito MVC, Blade). Este archivo se actualiza cada vez que se agrega
un paquete o requisito nuevo.

## Software base
| Componente | Versión requerida | Notas |
|---|---|---|
| PHP | **8.1, 8.2 o 8.3** | Laravel 10 exige PHP ≥ 8.1. Recomendado 8.2. No usar 8.4+ sin validar dependencias. |
| Composer | 2.x | Para instalar dependencias: `composer install --no-dev --optimize-autoloader` |
| MySQL / MariaDB | MySQL ≥ 5.7 / MariaDB ≥ 10.3 | Base de datos con `utf8mb4` / `utf8mb4_unicode_ci` |
| Servidor web | Apache 2.4 (mod_rewrite) o Nginx | |
| Node.js / npm | Node ≥ 18 (solo para compilar assets) | Puede compilarse en otro equipo y subir `public/build/` |

## Extensiones de PHP
Requeridas por Laravel 10:
- ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pcre, pdo, session, tokenizer, xml
- **pdo_mysql** (conexión a MySQL/MariaDB)

Requeridas por paquetes planeados (se confirmarán al instalarlos):
- **gd** y **mbstring** — barryvdh/laravel-dompdf (imágenes en PDF)
- **zip**, **xml**, **gd**, **simplexml**, **xmlreader**, **xmlwriter**, **iconv** — maatwebsite/excel (PhpSpreadsheet)
- **fileinfo** — validación MIME de evidencias

Recomendadas:
- intl, bcmath, opcache

## Configuración de PHP (php.ini)
- `upload_max_filesize = 12M` (evidencias de hasta 10 MB)
- `post_max_size = 64M`
- `memory_limit = 256M` (generación de PDF/Excel)
- `max_execution_time = 120`

## Permisos
- `storage/` y `bootstrap/cache/` con escritura para el usuario del servidor web
  (p. ej. `www-data`): `chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache`
- Las evidencias se guardan en `storage/app/` (disco `local`, privado). **No** ejecutar
  `storage:link` para evidencias; se sirven mediante controlador con autorización.

## Dominio / virtual host
- El `DocumentRoot` (Apache) o `root` (Nginx) **debe apuntar a la carpeta `public/`** del proyecto.
  Nunca exponer la raíz del proyecto.
- Apache: `AllowOverride All` para que funcione `public/.htaccess`.
- HTTPS recomendado (`APP_URL=https://...`).

## Despliegue (resumen)
```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env        # completar valores
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force # solo catálogos iniciales en producción
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
Variables obligatorias en `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `DB_*`, `MAIL_*`.

## Paquetes instalados
| Paquete | Restricción | Versión instalada | Requisito adicional |
|---|---|---|---|
| laravel/framework | ^10.10 | 10.50.3 | PHP ≥ 8.1 |
| laravel/sanctum | ^3.3 | (skeleton) | — |
| laravel/tinker | ^2.8 | (skeleton) | — |
| laravel/breeze (dev) | ^1.29 | 1.29.1 | Node ≥ 18 para compilar assets (Vite 5, Tailwind) |

## Avisos de seguridad aceptados (Laravel 10 sin soporte)
Laravel 10 está fuera de soporte de seguridad. `composer.json` ignora **solo** estos avisos
(`config.policy.advisories.ignore-id`), por requisito del proyecto de mantener Laravel 10:

| Aviso | Severidad | Mitigación obligatoria en el servidor/código |
|---|---|---|
| PKSA-3r5d-mb8f-1qw9 / PKSA-mdq4-51ck-6kdq (CVE-2026-48019) | Alta | Validación propia de correos que rechaza CR/LF |
| PKSA-m5cs-t1y6-qpcs | Media | La app no usa URLs firmadas temporales |
| PKSA-d5tc-s1qs-h781 (CVE-2026-102279) | Baja | **`APP_DEBUG=false` en producción** |

`composer install` requiere Composer ≥ 2.10 compatible con la clave `policy`; con versiones
anteriores de Composer 2.x la clave se ignora sin error.
