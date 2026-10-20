# Depuración de una aplicación híbrida

En una aplicación híbrida el fallo puede estar en el servidor, en el cliente, en la red entre los dos o en el servicio de un tercero. Lo primero es averiguar en qué lado está.

## 1. En el navegador (F12)

- **Consola**: errores de JavaScript (`Chart is not defined` significa que la librería no ha cargado) y mis `console.log`.
- **Red**: cada `fetch` con su URL, su código de estado y su respuesta. Si el código es 500, el problema está en PHP. Si es 404, la ruta está mal: me pasó con `../libs/` al arrancar `php -S` en la carpeta equivocada.
- **Elementos**: para ver si el DOM ha quedado como esperaba después de cambiarlo con JavaScript.

## 2. En el servidor (PHP)

- Llamar al PHP directamente en el navegador o con `curl -i` para ver el JSON sin el cliente en medio.
- `var_dump()` y `print_r()` mientras desarrollo, y quitarlos después.
- `error_log()` para escribir en el registro sin estropear el JSON de la respuesta.
- `display_errors` encendido solo en mi ordenador; en producción, apagado y con `log_errors`.
- Xdebug con VS Code para parar en una línea y ver las variables (puntos de ruptura).

## 3. En los datos

- Probar con un archivo pequeño cuyo resultado conozco (`datos de prueba/accesos-mini.csv`) antes de lanzar el grande.
- Comprobar los casos raros: filas rotas, archivos vacíos, un filtro que no devuelve nada.

## 4. En los servicios de terceros

- Poner siempre un tiempo máximo (`CURLOPT_TIMEOUT`) y un plan B. En la unidad 7, si la AEMET no responde, uso una predicción guardada.
- Guardar en caché lo que no cambia a menudo, para no depender del tercero en cada visita.

## Documentación

- Cada carpeta tiene su `000-Introducción.md` y el proyecto tiene `README.md`.
- Las clases llevan comentarios PHPDoc (`/** ... */`).
- Las librerías de terceros están listadas con su versión y licencia en `libs/LEEME.md`.
