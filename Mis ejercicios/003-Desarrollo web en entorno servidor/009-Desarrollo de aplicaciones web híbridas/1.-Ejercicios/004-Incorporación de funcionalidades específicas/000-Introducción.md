# Incorporación de funcionalidades específicas

**Comunidad Velnora**: una pequeña red social para los socios. En vez de reprogramar lo que ya resuelven otros, aprovecho la base de datos, `password_hash` de PHP y la librería Chart.js. Lo que añado yo son las funcionalidades típicas de una red social.

| Archivo | Funcionalidad |
|---|---|
| `001-Modelo de datos.md`, `002-creacion base de datos.sql` | Socios, publicaciones, reacciones y seguimientos |
| `003-datos de demostracion.php` | Genera 40 socios, 300 publicaciones, reacciones y seguimientos (contraseña de todos: `velnora123`) |
| `004-muro.php` | Muro de un socio: publicaciones de la gente a la que sigue, con el recuento de cada reacción |
| `005-reaccionar.php` | Poner o quitar una reacción sin recargar la página (lo usa el muro) |
| `006-actividad.html` + `006-datos actividad.php` | Gráficas de actividad con Chart.js |
| `007-a quien seguir.php` | Sugerencias de "amigos de mis amigos" |

La base de datos `comunidad.db` se crea sola la primera vez que se abre cualquiera de las páginas.
