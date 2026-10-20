Pulso con MVC · panel de gestión del gimnasio Velnora (versión 015)
Autor: Andres Ruiz

Reorganizo Pulso (014) en tres clases:
- modelo.php       -> VelnoraModelo: PDO, login con password_verify, roles, columnas, clave primaria y CRUD.
- controlador.php  -> VelnoraControlador: sesión, permisos por rol, acciones de alta, edición y borrado.
- vista.php        -> VelnoraVista: todo el HTML (login, panel, tabla y formulario).
- index.php        -> crea los tres objetos y los une. No tiene SQL ni HTML.

Mejoras respecto a la 014:
- PDO en lugar de SQLite3, con excepciones.
- Nombres de tabla y columna validados antes de meterlos en la consulta.
- Solo se guardan los campos que existen en la tabla.
- El tipo de cada input (email, date, number) se elige según el nombre de la columna.

Usuarios de prueba:
- velnora / velnora  (administración)
- recepcion / recepcion  (recepción)
