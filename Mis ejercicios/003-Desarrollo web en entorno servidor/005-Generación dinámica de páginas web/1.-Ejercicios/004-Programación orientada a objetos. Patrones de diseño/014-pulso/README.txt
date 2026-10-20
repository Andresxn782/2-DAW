Pulso · panel de gestión del gimnasio Velnora (versión 014, punto de partida de la unidad 5)
Autor: Andres Ruiz

Qué hace:
- Login contra la tabla usuarios con consulta preparada.
- Contraseñas guardadas con password_hash() y comprobadas con password_verify().
- Menú según el rol: el rol 1 (admin) ve todas las tablas, el rol 2 (recepción) solo socios, clases y reservas.
- Listado, alta, edición y borrado de cualquier tabla, detectando la clave primaria sola.
- La tabla pedida por la URL se valida contra las tablas del rol.
- Cerrar sesión vacía y destruye la sesión.

Usuarios de prueba:
- velnora / velnora  (administración)
- recepcion / recepcion  (recepción)

Tablas: socios, clases, entrenadores, planes, reservas, pagos, material, usuarios y roles.
Todos los datos son inventados.

Esta versión es la misma que la 013 de la unidad 4. La copio aquí como punto de partida:
todo el código está repartido en includes que mezclan consultas SQL y HTML.
En la 015 lo reorganizo en clases siguiendo el patrón MVC.
