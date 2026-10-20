# Proyecto de la unidad 4: Velnora Reservas

Proyecto resumen de la unidad, hecho por Andres Ruiz para el gimnasio Velnora.

Es el portal donde los socios reservan clases y los monitores ven quién se ha apuntado. Es distinto de Pulso (los ejercicios de clase): Pulso es el panel de gestión interno, y este es la web que usan los socios.

## Usuarios de prueba

La base de datos `reservas.db` se crea sola la primera vez, con estos usuarios:

| Usuario | Contraseña | Rol |
|---|---|---|
| marcos | marcos123 | monitor |
| lucia | lucia123 | socio |
| carlos | carlos123 | socio |

También se puede crear una cuenta nueva de socio desde "Hazte socio".

## Qué usa de la unidad

| Contenido | Dónde |
|---|---|
| Mantenimiento del estado con sesiones | `includes/config.php`, `index.php`, `logout.php` |
| Almacenar información en el cliente con cookies | Cookie `recordar` (login) y `tema` (`preferencias.php`) |
| Usuarios, perfiles y roles | Rol `socio` y rol `monitor`, `exigirRol()` |
| Autentificación con contraseñas cifradas | `password_hash` en `registro.php`, `password_verify` en `comprobarCredenciales()` |
| Seguridad | Consultas preparadas, `htmlspecialchars`, `session_regenerate_id`, token CSRF, cookie `httponly` |
| Pruebas y depuración | `pruebas.php`, modo `$depuracion`, `errores.log` |

## Cómo probarlo

1. Copiar la carpeta en `htdocs` y abrir `index.php`.
2. Entrar como `lucia`, reservar una clase y cancelarla. Pilates tiene solo 2 plazas para ver cómo se llena.
3. Entrar como `marcos` para ver los apuntados y crear una clase nueva.
4. Pruebas automáticas: en una terminal dentro de la carpeta, `php pruebas.php`.

Para empezar de cero, borrar `reservas.db` y se vuelve a crear.
