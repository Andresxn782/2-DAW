Pulso · panel de gestión del gimnasio Velnora (versión 012)
Autor: Andres Ruiz

Qué hace:
- Login contra la tabla usuarios con consulta preparada.
- La sesión es la única que da acceso (ya no me fío de la cookie 'llave').
- Menú según el rol: el rol 1 (admin) ve todas las tablas, el rol 2 (recepción) solo socios, clases y reservas.
- Listado, alta, edición y borrado de cualquier tabla, detectando la clave primaria sola.
- La tabla pedida por la URL se valida contra las tablas del rol.
- Cerrar sesión vacía y destruye la sesión.

Usuarios de prueba:
- velnora / velnora  (administración)
- recepcion / recepcion  (recepción)

Tablas: socios, clases, entrenadores, planes, reservas, pagos, material, usuarios y roles.
Todos los datos son inventados.
