# Cliente y servidor

## Cliente
- Es el ordenador o móvil del usuario, con su sistema operativo y un navegador (Chrome, Firefox, Edge...).
- El navegador solo entiende **HTML, CSS y JavaScript**.
- HTML aparece en 1991 y se estandariza en 1993. Por sí solo es **estático**: siempre enseña lo mismo.
- JavaScript le da algo de dinamismo, pero se ejecuta en el equipo del usuario.

## Servidor
El servidor existe para que el contenido sea:
1. **Dinámico**: cambia según quién entra, la hora, los datos...
2. **Centralizado**: los datos están en un único sitio para todos los usuarios.

## Pequeña línea de tiempo
| Año | Tecnología |
|-----|-----------|
| 1991-1995 | C, C++ y Perl mediante CGI (Common Gateway Interface) |
| 1995 | PHP (como herramienta personal; PHP 3 en 1998) |
| 1995 / 2000 | Java y después Java Enterprise (JEE) |
| ~2010 | Web interactiva: AJAX, `fetch` y sockets |
| Hoy | Node.js, Python, PHP moderno... |

## Ejemplo con el gimnasio Velnora
- **Cliente**: el socio abre la web en el móvil y pulsa "Reservar clase".
- **Servidor**: PHP recibe la petición, comprueba en la base de datos si quedan plazas y devuelve el HTML con la respuesta.
