# Artesanías Mayas — Tienda en línea con visor 3D, AR y recorrido VR

Tienda en línea de artesanías mayas con visor 3D de cada pieza, probador en realidad aumentada y recorrido virtual del taller.

**Autor:** Alex Fernando Sanchez Aparicio · Matrícula 202300196 · Universidad Politécnica de Quintana Roo

## Qué incluye

- **Catálogo** con filtros por categoría y rango de precio (Livewire).
- **Visor 3D** de cada pieza y **probador AR** (`<model-viewer>`, WebXR / Scene Viewer / Quick Look).
- **Recorrido VR del taller** con A-Frame, puntos de interés y narración por voz (`/recorrido`).
- **Carrito de compras** guardado en sesión.
- **Checkout** con **Stripe en modo prueba**: pago aprobado y tarjeta rechazada.
- **Panel de pedidos** con login de administrador (`/admin/pedidos`).
- **Video promocional** (`/promocional`) y **programa de procesamiento de audio y video** (Python + FFmpeg).

## Estructura del repositorio

| Carpeta | Contenido |
|---|---|
| `tienda/` | Aplicación web en Laravel (código del catálogo, carrito, checkout, panel, visor 3D y escenas AR/VR). |
| `modelos-3d/` | Archivos fuente de Blender (`.blend`), texturas (`.png`) y la animación de presentación (`animacion.blend`). Los modelos exportados (`.glb`) que usa la tienda están en `tienda/public/models/`. |
| `procesamiento-av/` | `procesar.py`: recorte, subtítulos, normalización de audio y compresión en varias resoluciones. |

Los videos pesados (`.mp4`, `.mov`, `.mkv`) no se versionan; solo se incluye el video promocional comprimido en `tienda/public/media/promocional.mp4`.

## Tecnologías (versiones del entorno de desarrollo)

- PHP 8.5.12 · Composer 2.11.1 · Laravel 13.44.1 + Livewire 4.4.10 · Vite + Tailwind CSS
- MySQL 8.4.9
- Blender 5.3.0 (modelado, texturas y animación)
- `<model-viewer>` (visor 3D y AR) · A-Frame 1.7.0 (VR)
- Stripe (modo prueba) · SDK `stripe/stripe-php`
- Python 3.13.7 · FFmpeg 9.0.2

## Instalación

Requisitos: PHP, Composer, Node.js con npm y MySQL.

```bash
git clone https://github.com/afsa3555/tienda-artesanias-mayas.git
cd tienda-artesanias-mayas/tienda

composer install
npm install
cp .env.example .env        # en PowerShell: copy .env.example .env
php artisan key:generate
```

1. Crea la base de datos en MySQL:

   ```sql
   CREATE DATABASE tienda_artesanias;
   ```

2. En `tienda/.env` configura la base de datos y la clave de prueba de Stripe:

   ```
   DB_DATABASE=tienda_artesanias
   DB_USERNAME=root
   DB_PASSWORD=
   STRIPE_SECRET=sk_test_pk_test_51UgDPNC4D2iun5Q24tLx2Clp0JTkcc1JpxMXy1C2ggsPcr4vj13dojbGDEbwdPRYbVOmcVLg6kdrRmlJ3lYnSyXP003ARmH5oK

3. Crea las tablas y los datos

   ```bash
   php artisan migrate --seed
   ```

4. Crea el usuario administrador desde `php artisan tinker`:

   ```php
   App\Models\User::create(['name' => 'Administrador', 'email' => 'admin@tienda.local', 'password' => Hash::make('12345')]);
   ```

5. Compila los estilos y arranca el servidor:

   ```bash
   npm run build
   php artisan serve
   ```

   Abre `http://localhost:8000`. Para desarrollar con recarga en caliente, ejecuta `npm run dev` en una segunda terminal.

## Rutas principales

| Ruta | Descripción |
|---|---|
| `/` | Catálogo con filtros y visor 3D / AR |
| `/recorrido` | Recorrido VR del taller |
| `/carrito` | Carrito de compras |
| `/checkout` | Datos del cliente y pago con Stripe (modo prueba) |
| `/promocional` | Video promocional |
| `/admin/login` · `/admin/pedidos` | Acceso y panel de pedidos del administrador |

## Probar el pago (modo prueba)

Con la clave `sk_test_...` configurada, usa las tarjetas de prueba de Stripe, con cualquier fecha futura, CVC de 3 dígitos y cualquier código postal:

- `4242 4242 4242 4242` → pago aprobado
- `4000 0000 0000 0002` → tarjeta rechazada

No se realizan cargos reales.

## Probar el AR en un celular

El AR necesita HTTPS y un dispositivo compatible (ARCore en Android, Quick Look en iPhone). Para exponer el servidor local:

```bash
cloudflared tunnel --url http://localhost:8000
```

Abre en el celular la dirección `https://...trycloudflare.com` que muestre el comando. Antes ejecuta `npm run build`: sin él, el celular no carga los estilos.

## Procesamiento de audio y video

Requiere Python y FFmpeg en el `PATH`. La voz sintética usa Windows (SAPI, voz `Microsoft Sabina Desktop`).

```bash
cd procesamiento-av
python procesar.py
```

Espera `animacion.mp4` en `~/multimedia/` y los clips en `~/multimedia/crudos/`. Genera el video promocional en 1080p, 720p y 480p en `~/multimedia/final/`.

## Metodología

El proyecto se gestionó con Scrum en GitHub Projects: historias de usuario como *issues*, cuatro sprints como *milestones* y estimación por puntos de historia. El documento del proyecto (PDF) incluye el backlog, los sprints, el burndown y las retrospectivas.

## Uso de inteligencia artificial

Se usó un asistente de IA como apoyo en el desarrollo. El registro detallado (qué se solicitó, qué se aceptó, corrigió o descartó, y cómo se verificó) está en la bitácora de IA, anexa al documento del proyecto en PDF.
