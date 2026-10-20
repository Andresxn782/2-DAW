# Prueba y documentación

Pruebo el motor CSV de `002-Recuperación y edición de información/003-minimotor/VelnoraCSV.php` con un conjunto de pruebas que crea una base de datos temporal, hace todas las operaciones y comprueba los resultados. Al terminar borra la carpeta temporal.

Ejecutar: `php "001-pruebas VelnoraCSV.php"`

Qué compruebo:

- crear base de datos y tabla, y que una tabla repetida no se sobrescribe;
- insertar, también textos con comas, comillas y saltos de línea;
- buscar, actualizar y eliminar devolviendo cuántas filas cambian;
- que un nombre de tabla con `../` se rechaza (no se puede salir de la carpeta de datos).

La documentación está en los comentarios PHPDoc de cada método de la clase.
