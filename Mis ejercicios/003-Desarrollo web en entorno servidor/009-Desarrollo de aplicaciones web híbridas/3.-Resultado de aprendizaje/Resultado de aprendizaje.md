# Resultado de aprendizaje 9

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 9

**9. Desarrolla aplicaciones web híbridas seleccionando y utilizando tecnologías, frameworks servidor y repositorios heterogéneos de información.**

## Criterios de evaluación

### a) Se han reconocido las ventajas que proporciona la reutilización de código y el aprovechamiento de información ya existente.

En `1.-Ejercicios/002-Reutilización de código e información/` lo practico de varias formas:

- `001-servidor.php` publica un dato y `002-cliente.html` lo reutiliza desde otra página. En `003-ejemplos.md` explico casos reales (mapas, pagos, inicio de sesión, el tiempo).
- `004-reutilizar mi motor CSV.php` hace un `require` de la clase `VelnoraCSV` que programé en la unidad 6, sin copiarla. Si la mejoro allí, la mejora llega aquí.

En el proyecto también reutilizo:

- la carátula SVG de Velnora TV (`miniatura.php`) y su catálogo `entrenos.json`;
- la lectura del XML de la AEMET de la unidad 7;
- Chart.js, en vez de dibujar los gráficos a mano.

Las ventajas que he visto:

- se tarda menos;
- el código ya está probado;
- los datos de otros (la AEMET) son mejores que los que yo podría inventar;
- si un trozo se arregla, se arregla en todos los sitios.

### b) Se han identificado tecnologías y frameworks relacionados con la creación de aplicaciones web híbridas.

En `1.-Ejercicios/001-Tecnologías y frameworks/000-aplicaciones hibridas.md` describo:

- qué es una aplicación híbrida (mashup);
- las tecnologías que la hacen posible: APIs REST, JSON, XML, `fetch` y CORS, cURL en el servidor, cachés;
- algunos frameworks y librerías: jQuery, Chart.js, Leaflet, Laravel, Symfony.

Después uso jQuery en las tres páginas de ejemplo:

- `001-introduccion.html`;
- `002-metodo ready.html`;
- `003-metodo load.html`, que carga `consejo.txt`.

Y `004-getJSON.html` lee `frases.json`. Las librerías están copiadas en `1.-Ejercicios/libs/` para no depender de internet.

### c) Se ha creado una aplicación web que recupere y procese repositorios de información ya existentes.

Velnora Hub (`2.-Proyecto/velnora-hub`) recupera cinco repositorios distintos y los procesa:

| Repositorio | Formato | Qué hace con él |
|---|---|---|
| Horario | SQLite | Filtra las clases que aún no han empezado. |
| Predicción de la AEMET | XML (cURL) | Decide si hoy se puede entrenar fuera. |
| Accesos del torno | CSV | Calcula la media de gente por hora. |
| Rutas | GeoJSON | Calcula los km con el haversine y las dibuja en SVG. |
| Catálogo de vídeos | JSON | Elige tres según el tiempo. |

También en los ejercicios:

- en `1.-Ejercicios/003-Utilización de información proveniente de repositorios/`, el catálogo de Velnora TV se lee desde un JSON de 60 vídeos;
- se va mejorando en cuatro pasos: catálogo, catálogo con estilo, botón «cargar más», y scroll infinito con filtros.

### d) Se han creado repositorios específicos a partir de información existente en almacenes de información.

- `repositorio/generar.php` crea `entrenos.json`, el repositorio de Velnora TV, con 60 vídeos de seis categorías.
- `repositorio/miniatura.php` crea las carátulas SVG a partir de esos datos.
- En el apartado 004, `003-datos de demostracion.php` crea `comunidad.db` (socios, publicaciones, seguidores y reacciones) a partir del modelo de `001-Modelo de datos.md` y `002-creacion base de datos.sql`.
- En el apartado 005, `002-generar accesos.php` genera unos 234.000 accesos en `accesos.csv`, con altas y bajas de socios.
- En el proyecto:
  - `FuenteAfluencia::generar()` crea los 28 días de accesos;
  - `FuenteClases` crea el horario en `velnora.db`;
  - `Cache` guarda un repositorio propio en JSON con lo ya calculado, para no repetirlo.

### e) Se han utilizado librerías de código para incorporar funcionalidades específicas a una aplicación web.

En `1.-Ejercicios/004-Incorporación de funcionalidades específicas/` añado funcionalidades de red social a Velnora:

- `004-muro.php`: muro de publicaciones de la gente a la que sigues, con «hace 5 minutos» (`haceCuanto()` en `conexion.php`);
- `005-reaccionar.php`: reacciones con `fetch`, protegidas con un token en la cabecera `X-Token`;
- `006-actividad.html`: gráfico de actividad con **Chart.js**;
- `007-a quien seguir.php`: sugerencias de a quién seguir (amigos de mis amigos).

En el proyecto, Chart.js dibuja la afluencia y marca en otro color la hora actual.

### f) Se han programado servicios y aplicaciones web utilizando como base información y código generados por terceros.

- La predicción del tiempo es de la AEMET (un tercero). `FuenteTiempo` la descarga con cURL, lee el XML con SimpleXML y la convierte en una recomendación.
  - Si la AEMET no responde en 6 segundos, se usa `datos/prediccion-ejemplo.xml` y la tarjeta lo dice.
  - Se guarda una hora en caché para no hacer una petición en cada visita.
  - El pie de la página cita la fuente («Datos del tiempo: © AEMET»).
- El código de terceros es jQuery y Chart.js.
- `api.php` sirve cada fuente por separado, así que si un tercero falla, el resto de la página sigue funcionando (responde 502 solo en esa fuente).

### g) Se han utilizado librerías de código relacionadas con Big Data e inteligencia de negocios.

En `1.-Ejercicios/005-Utilización de librerías de código relacionadas con Big Data e inteligencia de negocios/`:

- En `001-Big Data e inteligencia de negocios.md` explico:
  - las "V" (volumen, velocidad, variedad, veracidad y valor);
  - ETL, KPI y cuadro de mando;
  - herramientas como Hadoop, Spark, Power BI, Metabase y Pandas.
- `002-generar accesos.php` crea unos 234.000 accesos.
- La clase `AnalizadorAccesos.php` lee el CSV con un **generador** (`yield`) y saca los indicadores, guardados en caché.
- `003-memoria.php` compara la memoria de leer el archivo entero con la de leerlo con el generador.
- El cuadro de mando (`004-cuadro de mando.html` con `004-datos cuadro de mando.php`) muestra:
  - los KPI (visitas, media diaria, socios distintos, duración media, hora punta, mejor hora para ir, día récord);
  - un mapa de calor por día y hora;
  - gráficos de Chart.js: visitas y socios activos por mes (con altas y bajas, para que no salga siempre el mismo número) y visitas por plan.

En el proyecto, la tarjeta «¿Cuánta gente hay?» aplica lo mismo a una decisión del socio: a qué hora le conviene ir.

### h) Se han probado, depurado y documentado las aplicaciones generadas.

En `1.-Ejercicios/006-Prueba, depuración y documentación/`:

- `001-depuracion.md` explica:
  - `var_dump`;
  - `error_log`;
  - la consola y la pestaña Red del navegador;
  - Xdebug.
- `002-registro de errores.php` guarda los errores en un archivo de registro.
- `003-pruebas.php` tiene 20 pruebas del analizador con un CSV pequeño de datos conocidos (`datos de prueba/accesos-mini.csv`).

En el proyecto, `pruebas.php` tiene 32 pruebas, y pasan todas. Comprueban:

- las fuentes, con fecha fija;
- que funcione sin la AEMET;
- la caché, incluida la caducidad;
- la API por HTTP.

La página la he probado en el navegador a tamaño de ordenador y de móvil, sin errores en la consola.

Gracias a las pruebas encontré dos errores y los corregí:

- en el scroll infinito, el catálogo se paraba si el centinela seguía a la vista después de cargar;
- la caché no veía la nueva fecha del archivo por la caché de `stat` de PHP, y lo arreglé con `clearstatcache()`.

Todo está documentado con `README.md`, comentarios en el código y un `000-Introducción.md` en cada apartado.
