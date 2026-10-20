# Documentar una API

## OpenAPI (antes Swagger)

Es el estándar más usado para describir APIs REST. Es un archivo JSON o YAML que dice:

- la dirección base del servicio (`servers`);
- cada ruta y cada método (`paths`);
- los parámetros que acepta y si son obligatorios;
- qué devuelve en cada código de estado (`responses`);
- la forma de los datos (`components/schemas`);
- cómo se autentica (`securitySchemes`, en mi caso una cabecera `X-API-Key`).

Con ese archivo, herramientas como **Swagger UI** o **Redoc** generan una web de documentación en la que se pueden probar las peticiones, y otras herramientas generan clientes en muchos lenguajes.

## Otros frameworks

| Herramienta | Qué documenta |
|---|---|
| Swagger UI / Redoc | APIs REST a partir de OpenAPI. |
| phpDocumentor | Código PHP a partir de los comentarios `/** ... */` (lo usé en la unidad 5). |
| Postman | Genera documentación desde una colección de peticiones. |
| WSDL | Describe servicios SOAP en XML. |

## Lo que hago yo

- `openapi.json`: la descripción de la API de socios del apartado 003.
- `002-documentacion.php`: lee `openapi.json` con `json_decode` y pinta la documentación, así no tengo que escribirla dos veces. Si cambio el JSON, la página cambia sola.
