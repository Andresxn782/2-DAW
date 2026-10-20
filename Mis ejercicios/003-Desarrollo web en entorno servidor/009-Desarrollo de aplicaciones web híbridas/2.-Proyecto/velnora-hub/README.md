# Velnora Hub

Proyecto de la unidad 9 (Desarrollo de aplicaciones web híbridas). Andres Ruiz.

Es la pantalla de inicio del socio de Velnora: en una sola página junta información que sale de cinco sitios distintos y la combina para responder a una pregunta sencilla, **¿cómo entreno hoy?**

| Tarjeta | De dónde sale | Tipo de repositorio |
|---|---|---|
| Clases de hoy | Base de datos propia del gimnasio | SQLite |
| ¿Entrenar fuera? | Predicción de la AEMET para Madrid | XML de un tercero (internet) |
| ¿Cuánta gente hay? | Accesos del torno de las últimas 4 semanas | CSV grande (datos masivos) |
| Rutas para correr | Rutas del club de running | GeoJSON |
| Velnora TV recomienda | Catálogo de vídeos (apartado 003 de los ejercicios) | JSON |

El ejemplo de clase junta varias fuentes en una página. Este proyecto va un poco más allá:

- las fuentes **se mezclan**: los vídeos recomendados dependen del tiempo (si llueve, entrenos cortos de interior; si hace bueno, movilidad y yoga) y las clases al aire libre se marcan cuando se puede entrenar fuera;
- cada fuente se pide por separado (`api.php?fuente=...`), así que si la AEMET no responde solo esa tarjeta lo nota, y además se usa una copia de ejemplo y se dice en la tarjeta;
- hay una **caché** en archivos con caducidad distinta para cada fuente, para no llamar a la AEMET ni recorrer el CSV en cada visita;
- el CSV se lee con un **generador** (`yield`), fila a fila, sin cargarlo entero en memoria;
- las rutas se dibujan en SVG a partir de las coordenadas y la distancia se calcula con la fórmula del haversine, sin depender de un servicio de mapas;
- el gráfico de afluencia usa Chart.js, copiado en `libs/` para que funcione sin internet.

## Cómo se usa

1. Con XAMPP, abrir la carpeta `velnora-hub` en el navegador (`index.php`).
2. La primera vez se crean solos `datos/velnora.db` (horario) y `datos/accesos.csv` (28 días de accesos), y se llenan los archivos de `cache/`.
3. Para empezar de cero basta con borrar esos archivos; ninguno se sube a Git.

PHP necesita las extensiones `pdo_sqlite`, `curl` y `simplexml` (vienen activadas en XAMPP).

## Archivos

| Archivo | Qué hace |
|---|---|
| `index.php` | Página principal. El servidor pinta ya las clases de hoy; el resto lo rellena JavaScript. |
| `api.php` | `api.php?fuente=clases\|tiempo\|rutas\|afluencia\|videos`. Responde JSON con `datos`, `desde_cache` y `guardado`; 404 si la fuente no existe y 502 si falla. |
| `inicio.php` | Carga las clases y la función `consultar()`, que decide la caché de cada fuente. |
| `config.php` | Rutas, municipio de la AEMET (28079, Madrid) y tiempos de caché. |
| `Clases/Fuente.php` | Interfaz común: `nombre()` y `obtener()`. |
| `Clases/Cache.php` | `recordar(clave, segundos, función)` y `vaciar()`. |
| `Clases/FuenteClases.php` | Horario en SQLite: las clases de hoy que aún no han empezado y las de mañana. |
| `Clases/FuenteTiempo.php` | Descarga el XML de la AEMET con cURL y decide si se puede entrenar fuera. |
| `Clases/FuenteAfluencia.php` | Lee el CSV del torno y saca la media de cada hora para el día de la semana de hoy. |
| `Clases/FuenteRutas.php` | Lee el GeoJSON, calcula los km y prepara el trazado SVG. |
| `Clases/FuenteVideos.php` | Elige tres vídeos de Velnora TV según el tiempo. Cambia cada día. |
| `miniatura.php` | Carátula SVG de cada vídeo, reutilizada de los ejercicios. |
| `js/hub.js` | Pide cada fuente con `fetch` y pinta su tarjeta. |
| `datos/` | `rutas.geojson`, `entrenos.json` y `prediccion-ejemplo.xml` (copia por si falla la AEMET). |
| `pruebas.php` | 32 pruebas de las fuentes, la caché y la API por HTTP. |

## Pruebas

```
php pruebas.php
```

Las pruebas de las fuentes usan una carpeta temporal y fechas fijas (un martes a las 11:00), para que salgan igual cualquier día. Las de la API arrancan un servidor `php -S` en la carpeta del proyecto.
