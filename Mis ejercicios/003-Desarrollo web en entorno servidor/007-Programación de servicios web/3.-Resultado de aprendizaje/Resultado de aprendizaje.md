# Resultado de aprendizaje 7

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 7

**7. Desarrolla servicios web reutilizables y accesibles mediante protocolos web, verificando su funcionamiento.**

## Criterios de evaluación

### a) Se han reconocido las características propias y el ámbito de aplicación de los servicios web.

En `1.-Ejercicios/001-Tecnologías y protocolos implicados/001-Definiciones.md` explico qué es un servicio web:

- Es una API a la que se llega por HTTP y que devuelve datos para otro programa, no una página para personas.
- Funciona como un contrato: el servidor se compromete a responder en un formato y el cliente a interpretarlo.
- Se usa cuando varios sistemas tienen que compartir información. En Velnora, la web de reservas, la app del móvil y la recepción leen las mismas clases desde la API del proyecto.
- Otro ámbito es consumir datos públicos de terceros, como la predicción de la AEMET del apartado 004.

### b) Se han reconocido las ventajas de utilizar servicios web para proporcionar acceso a funcionalidades incorporadas a la lógica de negocio de una aplicación.

- La lógica de negocio está una sola vez en el servidor. En el proyecto, las reglas del aforo, las reservas repetidas y los permisos están en `ServicioClases.php`, y cualquier cliente las respeta sin programarlas otra vez.
- Es independiente del lenguaje: el panel en PHP con cURL y el probador de la documentación en JavaScript con `fetch` usan la misma API.
- Separa el cliente del servidor. El panel de recepción no abre la base de datos y se podría cambiar por otro sin tocar la API.
- Usa HTTP, que funciona en cualquier red, y con CORS se puede llamar desde otras webs.

### c) Se han identificado las tecnologías y los protocolos implicados en el consumo de servicios web.

- HTTP como transporte, la URL para el recurso y el método para la acción.
- Las cabeceras: `Content-Type` para decir qué se envía, `Accept` para pedir JSON o XML, `X-API-Key` para autenticarse, `Location` en lo que se crea, `Allow` en el 405 y las de CORS.
- Los códigos de estado. El proyecto usa 200, 201, 204, 400, 401, 403, 404, 405, 409, 422 y 500.
- En el lado cliente, cURL en PHP (`curl_init`, `CURLOPT_HTTPHEADER`, `CURLOPT_CUSTOMREQUEST`, `CURLINFO_HTTP_CODE`), y `curl` y `fetch` fuera de PHP.

### d) Se han utilizado los estándares y arquitecturas más difundidos e implicados en el desarrollo de servicios web.

- La API del proyecto sigue la arquitectura REST: cada cosa es un recurso con su ruta (`/clases/4/reservas`) y se usan `GET`, `POST`, `PUT` y `DELETE`.
- En `002-…/003-formatos de intercambio.php` saco los mismos datos en JSON, XML y CSV. La API del proyecto responde en JSON o, si se pide, en XML.
- En `001-Definiciones.md` comparo REST con SOAP (sobre XML y WSDL) y con GraphQL.
- La documentación sigue el estándar OpenAPI 3.

### e) Se han programado servicios web.

- `002-…/001-API` y `002-API` montan la API más simple, primero con el JSON escrito a mano y luego con `json_encode`.
- `003-Generación de un servicio web/003-API/SoftwareA/api.php` lee los socios de SQLite, exige la clave en `X-API-Key` (comparada con `hash_equals`), filtra por plan y devuelve 401 o 404 cuando toca.
- El proyecto `2.-Proyecto/velnora-api` es una API REST completa:
  - un controlador frontal (`api/index.php`) con la tabla de rutas;
  - el CRUD de clases y las reservas;
  - dos niveles de permiso (app y admin);
  - validación con un error por campo;
  - transacciones para no vender dos veces la última plaza;
  - respuesta en JSON o XML.

### f) Se ha verificado el funcionamiento de los servicios web.

- En `005-…/003-probar api.php` arranco un servidor de PHP y compruebo nueve casos de la API de socios: sin clave, clave falsa, clave buena, tipo de contenido, filtro y 404.
- `pruebas.php` del proyecto hace 41 comprobaciones con peticiones HTTP reales y una base de datos temporal, incluidos los casos malos: JSON roto, permisos, clase completa, aforo menor que las reservas y borrado en cascada.
- En `004-herramientas de prueba.md` explico cómo probar con el navegador (F12), `curl -i`, Postman y scripts propios.
- La documentación del proyecto tiene un probador para lanzar cada operación desde el navegador.

### g) Se ha consumido el servicio web.

- `002-API/SoftwareB/llamada.php` y `003-API/SoftwareB/llamada.php` consumen las APIs de los apartados anteriores con cURL, la segunda enviando la clave buena y una falsa.
- En `004-Interface de un servicio web` consumo un servicio real, la predicción XML de la AEMET para Madrid:
  - descargo y guardo la predicción con fecha;
  - la leo con SimpleXML;
  - decido si la clase de running se hace fuera;
  - pinto una gráfica SVG de temperaturas y lluvia.
- En `005-…/002-guardar.php` dejo la descarga programada con cron o con el Programador de tareas de Windows.
- En el proyecto, `cliente/ClienteApi.php` y `cliente/index.php` hacen todas las operaciones a través de la API y enseñan la petición y la respuesta en bruto.

### h) Se ha documentado un servicio web.

- `006-Frameworks de documentación/001-OpenAPI.md` explica OpenAPI, Swagger UI, Redoc, phpDocumentor y WSDL.
- `openapi.json` describe la API de socios, y `002-documentacion.php` genera la página de documentación a partir de ese archivo.
- En el proyecto, `docs/openapi.json` describe las diez operaciones con sus parámetros, cuerpos, respuestas, esquemas y la seguridad. `docs/index.php` lo convierte en una página con un probador.
- El `README.md` del proyecto tiene la tabla de rutas, las claves, ejemplos con curl y cómo pasar las pruebas.
