# PROGRESS — Aplicativo de actividades docentes (Depto. Informática y Computación, UNAL)

Stack: Laravel 10 · Blade · Breeze (Blade) · Tailwind · MySQL/MariaDB · dompdf · maatwebsite/excel

Leyenda: `[x]` hecha y probada · `[~]` implementada, sin probar · `[ ]` pendiente · `[!]` bloqueada

---

## F0. Entorno
- [x] Revisar herramientas instaladas en el PC de desarrollo (ver "Entorno detectado")
- [x] Crear PROGRESS.md
- [x] Crear REQUISITOS_SERVIDOR.md
- [x] Inicializar Git + .gitignore raíz
- [x] Instalar PHP 8.2.34 portátil (`C:\tools\php82`) + Composer 2.10.3 (en PATH de usuario)
- [x] Crear proyecto Laravel 10 en `app-docentes/` (skeleton v10.3.3, framework 10.50.3)
- [x] Verificar `php artisan --version` = Laravel Framework 10.50.3
- [x] Configurar `.env` y `.env.example` documentado para el servidor (locale `es`, zona `America/Bogota`)
- [x] Instalar Laravel Breeze `^1.29` (stack Blade) + `npm install && npm run build`
- [x] Configurar `phpunit.xml` para pruebas con SQLite en memoria — `php artisan test`: 25 pruebas OK

## F1. Modelo de datos
- [ ] Migraciones: users (campos extra), programas_curriculares, periodos_academicos, asignaturas, grupos
- [ ] Migraciones: tipos_actividad, entidades_externas, actividades (soft deletes), actividad_grupo
- [ ] Migraciones: evidencias, criterios_acreditacion, actividad_criterio
- [ ] Modelos Eloquent con relaciones y casts
- [ ] Factories
- [ ] Seeders: 1 admin, 3 profesores, 2 periodos, 6 asignaturas, grupos, catálogo de tipos de actividad
- [ ] Pruebas de relaciones y seeders

## F2. Roles y autorización
- [ ] Middleware de rol (registrado en `app/Http/Kernel.php`)
- [ ] Policies: Grupo, Actividad, Evidencia (+ admin bypass con `before`)
- [ ] Regla: periodo cerrado = solo lectura (salvo reapertura por admin)
- [ ] Menú de navegación según rol
- [ ] Pruebas de acceso por rol

## F3. CRUD admin
- [ ] Usuarios
- [ ] Periodos académicos (activar uno solo / cerrar / reabrir)
- [ ] Programas curriculares
- [ ] Asignaturas
- [ ] Grupos (asignación de profesor)
- [ ] Catálogo tipos de actividad
- [ ] Catálogo entidades externas
- [ ] Catálogo criterios de acreditación (vacío por defecto)
- [ ] Pruebas Feature de cada CRUD

## F4. Mis materias
- [ ] Vista "Mis materias" del periodo activo (solo grupos del profesor)
- [ ] Pruebas Feature

## F5. Actividades
- [ ] CRUD de actividades (borrador / registrada), multigrupo
- [ ] Evidencias: subida a disco `local` privado, máx. 10 MB, jpg/png/pdf/docx/xlsx
- [ ] Descarga de evidencias vía controlador autorizado
- [ ] Asociación con criterios de acreditación
- [ ] Pruebas Feature (incl. profesor no accede a datos de otro)

## F6. Resumen semestral
- [ ] Servicio de agregación del resumen (por periodo)
- [ ] Vista en pantalla con filtro de periodo
- [ ] Export PDF (encabezado institucional configurable)
- [ ] Export Excel (una hoja por sección)
- [ ] Pruebas Feature

## F7. Consolidado del departamento (admin)
- [ ] Vista consolidada por periodo
- [ ] Export PDF / Excel
- [ ] Pruebas Feature

## F8. Endurecimiento
- [ ] Form Requests en todos los formularios
- [ ] Mensajes de validación en español (`lang/es`)
- [ ] Paginación y búsqueda en listados
- [ ] Manejo de errores (`app/Exceptions/Handler.php`, vistas 403/404/419/500)
- [ ] Revisión de seguridad: CSRF, autorización en cada ruta, subida de archivos

## Criterio de salida
- [ ] Todas las tareas marcadas
- [ ] `php artisan --version` = Laravel 10.x
- [ ] `php artisan test` pasa completo
- [ ] Flujo profesor de prueba: login → grupos → actividad con evidencia y criterio → PDF y Excel
- [ ] Prueba explícita: profesor no accede a datos de otro

---

## Entorno detectado (2026-10-03, PC de desarrollo Windows)
| Herramienta | Estado |
|---|---|
| PHP | 8.2.34 NTS x64 portátil en `C:\tools\php82` (instalado por el agente; no hay XAMPP) |
| Composer | 2.10.3 (`C:\tools\php82\composer.bat`) |
| MySQL/MariaDB | **No instalado** (las pruebas usan SQLite en memoria) |
| Node / npm | v24.14.0 / 11.9.0 |
| Git | 2.53.0 |

## Decisiones
- 2026-10-03: Inicialmente opción C (solo estructura). Luego el usuario aprobó instalar PHP 8.2 y
  Composer de forma portátil. No se usó XAMPP.
- 2026-10-03: Proyecto en `app-docentes/`, skeleton `laravel/laravel` v10.3.3 → `laravel/framework` 10.50.3.
- 2026-10-03 **RIESGO ACEPTADO (aprobado por el usuario):** Laravel 10 ya no recibe parches de
  seguridad y Composer 2.10 bloquea su instalación. Se ignoran en `composer.json`
  (`config.policy.advisories.ignore-id`) **solo** estos avisos sin parche en 10.x:
  - PKSA-3r5d-mb8f-1qw9 / PKSA-mdq4-51ck-6kdq (CVE-2026-48019, alta): CRLF en regla `email`
    → mitigación: regla de validación propia que rechace `\r`/`\n` (F8).
  - PKSA-m5cs-t1y6-qpcs (media): confusión de ruta en URLs firmadas temporales
    → mitigación: no usar URLs firmadas; evidencias vía controlador autorizado.
  - PKSA-d5tc-s1qs-h781 (CVE-2026-102279, baja): XSS en página de depuración
    → mitigación: `APP_DEBUG=false` en el servidor.
  Cualquier aviso nuevo volverá a bloquear `composer update` (alerta deseada).
- Breeze fijado en `^1.29` (v1.29.1 es la última que soporta Laravel 10; 2.x exige Laravel 11).
- `config/app.php`: `timezone`, `locale`, `fallback_locale` leen `APP_TIMEZONE`/`APP_LOCALE`
  (por defecto `America/Bogota`/`es`); `faker_locale` = `es_ES`.
- Pruebas automatizadas con SQLite en memoria (`phpunit.xml`), sin depender de MySQL.

## Supuestos
- Nombre de la carpeta del proyecto Laravel: `app-docentes/` (cambiable).

## Pendientes / bloqueos
- Instalar MySQL/MariaDB (XAMPP) para pruebas manuales locales (`php artisan migrate --seed`).
- Breeze trae registro público (`/register`): se restringirá en F2 (alta de usuarios solo por admin).
- Breeze usa URL firmada temporal en `verification.verify` (verificación de correo). Como la
  verificación no está activada (User no implementa `MustVerifyEmail`), en F2 decidir: retirar
  esas rutas (mitigación PKSA-m5cs) o mantenerlas aceptando el riesgo.
- Traducción de vistas de Breeze y mensajes de validación al español: F8 (algunas ya en F2/F3).
- Pendiente institucional: textos y logo del encabezado institucional del PDF (configurable).
- Pendiente institucional: contenido de criterios/factores de acreditación (lo carga el admin).
- Pendiente institucional: servidor SMTP para recuperación de contraseña.
