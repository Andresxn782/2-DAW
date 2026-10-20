# Resultado de aprendizaje 4

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 4

**4. Desarrolla aplicaciones web embebidas en lenguajes de marcas analizando e incorporando funcionalidades según especificaciones.**

## Criterios de evaluación

### a) Se han identificado los mecanismos disponibles para el mantenimiento de la información que concierne a un cliente web concreto y se han señalado sus ventajas.

HTTP no guarda estado: cada petición es independiente. Para recordar a un cliente hay varios mecanismos:

| Mecanismo | Dónde se guarda | Ventajas | Inconvenientes |
|---|---|---|---|
| Sesiones | Servidor | El usuario no puede modificar los datos | Ocupan memoria o disco en el servidor |
| Cookies | Navegador | Duran aunque se cierre el navegador | El usuario puede verlas y cambiarlas |
| Parámetros GET y campos ocultos | URL o formulario | Muy simples | Visibles y fáciles de manipular |
| Base de datos | Servidor | Permanente | Necesita identificar antes al usuario |

En Velnora Reservas (`2.-Proyecto/velnora-reservas/`), `preferencias.php` muestra una tabla con lo que guarda la aplicación en cada sitio.

### b) Se han utilizado mecanismos para mantener el estado de las aplicaciones web.

Uso `session_start()` y `$_SESSION`:

- Al entrar, guardo el usuario y su rol en la sesión.
- Cada página comprueba la sesión con `exigirLogin()`.
- `logout.php` vacía y destruye la sesión.
- Los mensajes de "Reserva hecha" se guardan en la sesión antes de redirigir, para que al recargar la página no se repita la acción.

Dónde lo he trabajado: `1.-Ejercicios/001-Mantenimiento del estado/` (`003-sesion inicio.php` a `007-escritorio.php`) y las versiones `008-pulso` y `009-pulso`.

### c) Se han utilizado mecanismos para almacenar información en el cliente web y para recuperar su contenido.

Uso dos cookies, que creo con `setcookie()` y leo con `$_COOKIE`:

- `recordar` guarda el nombre de usuario durante 30 días y rellena el login.
- `tema` guarda si el usuario prefiere el tema claro o el oscuro durante un año.

Nunca guardo la contraseña en una cookie, y valido el valor de `tema` porque el usuario puede cambiarlo.

Dónde lo he trabajado: `1.-Ejercicios/002-Almacenamiento y recuperación de información en el cliente web/` (`001-poner cookie.php`, `002-leer cookie.php`, `003-solo quiero una cookie.php`) y `preferencias.php` del proyecto.

### d) Se han identificado y caracterizado los mecanismos disponibles para la autentificación de usuarios.

Los mecanismos que he identificado son:

- usuario y contraseña comprobados contra una base de datos (el que uso);
- autenticación HTTP básica del servidor;
- tokens (por ejemplo JWT en APIs);
- inicio de sesión con terceros (OAuth: Google, GitHub);
- doble factor.

Para guardar contraseñas no se deben usar en texto plano ni con MD5, sino con `password_hash`, que añade una sal y usa un algoritmo lento. Para los permisos, los usuarios tienen un perfil o rol que decide qué pueden ver.

Dónde lo he trabajado: `1.-Ejercicios/003-Seguridad usuarios, perfiles, roles/` (de `010-pulso` a `012-pulso`) y `1.-Ejercicios/004-Autentificación de usuarios/013-pulso`.

### e) Se han escrito aplicaciones que integren mecanismos de autentificación de usuarios.

Velnora Reservas tiene registro de socios, login y dos roles:

- En `registro.php` la contraseña se guarda con `password_hash`.
- En el login se comprueba con `password_verify` y se cambia el id de sesión con `session_regenerate_id`.
- El rol `socio` reserva y cancela clases; el rol `monitor` crea clases y ve los apuntados. `exigirRol()` devuelve un error 403 si un socio intenta entrar en `monitor.php`.
- Todas las consultas son preparadas, la salida se escapa con `htmlspecialchars` y los formularios llevan un token contra CSRF.

En los ejercicios, Pulso también tiene login contra la tabla `usuarios`, roles y contraseñas cifradas.

### f) Se han utilizado herramientas y entornos para facilitar la programación, prueba y depuración del código.

Herramientas que he usado:

- XAMPP para ejecutar en local y VS Code para programar.
- `php -l` para comprobar la sintaxis de cada archivo.
- Las herramientas del navegador (F12, pestaña Aplicación) para ver las cookies.
- Un modo `$depuracion` en `includes/config.php` que muestra los errores mientras desarrollo.
- `error_log()`, que guarda en `errores.log` los intentos de login fallidos.

He escrito `pruebas.php`, que se ejecuta con `php pruebas.php` y comprueba 12 casos. Entre ellos están la contraseña correcta e incorrecta, un intento de inyección SQL, las plazas libres de una clase llena, el token CSRF y el escape de HTML. Las 12 pruebas pasan.

Dónde lo he trabajado: `1.-Ejercicios/005-Pruebas y depuración/` y `2.-Proyecto/velnora-reservas/pruebas.php`.
