# Aplicaciones web híbridas

Una aplicación web **híbrida** (o *mashup*) no lo hace todo ella sola: junta código y datos que ya existen en otros sitios y les añade algo propio.

Ejemplos en el mundo del deporte:

- Strava junta el GPS del móvil, los mapas de OpenStreetMap o Mapbox y sus propios datos de segmentos y clasificaciones.
- Una web de gimnasio que muestra el tiempo de la AEMET, el mapa de Google y el horario que sale de su propia base de datos.
- Un cuadro de mando que lee los accesos del torno y los dibuja con una librería de gráficas.

## Tecnologías que lo permiten

| Pieza | Ejemplos | Para qué |
|---|---|---|
| Formatos de intercambio | JSON, XML, CSV, GeoJSON | Que los datos se entiendan entre sistemas |
| APIs y servicios web | REST, la AEMET, OpenStreetMap | Pedir datos a otros |
| Librerías de cliente | jQuery, Chart.js, Leaflet, D3 | No reprogramar lo que ya está hecho |
| Frameworks de servidor | Laravel, Symfony, Slim | Rutas, plantillas, acceso a datos, seguridad |
| Gestores de paquetes | Composer (PHP), npm (JavaScript) | Instalar y actualizar librerías |
| Repositorios de datos | Bases de datos, archivos, portales de datos abiertos | El origen de la información |

## jQuery

jQuery es una librería de JavaScript muy usada durante años. Lo que hace:

- buscar elementos con selectores de CSS: `$("#contenedor")`;
- cambiarlos: `.text()`, `.html()`, `.addClass()`;
- responder a eventos: `.on("click", ...)`;
- pedir datos al servidor: `.load()`, `$.getJSON()`.

Hoy casi todo eso se puede hacer con JavaScript normal (`querySelector`, `fetch`), pero se sigue encontrando en muchísimas webs, por ejemplo en las hechas con WordPress. En los ejercicios hago cada cosa de las dos formas.
