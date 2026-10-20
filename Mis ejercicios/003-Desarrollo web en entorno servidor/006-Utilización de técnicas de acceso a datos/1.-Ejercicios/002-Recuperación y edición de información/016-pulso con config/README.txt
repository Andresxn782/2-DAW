Pulso con MVC y configuración · gimnasio Velnora (versión 016)
Autor: Andres Ruiz

Estructura:
- config.php: nombre, subtítulo, logo, color corporativo y ruta de la base de datos.
- leeconfiguracion.php: devuelve la parte visual de la configuración en JSON.
- Clases/modelo.php: VelnoraModelo, acceso a datos y CRUD.
- Clases/controlador.php: VelnoraControlador, login, sesión, permisos y acciones.
- Clases/vista.php: VelnoraVista, genera la interfaz con los datos de config.php.
- index.php: punto de entrada que une el MVC.
- login.php y matarsesion.php: entrada y salida de la sesión.

Para cambiar la identidad visual basta con editar config.php (por ejemplo, el color a '#ec4899').

Usuarios de prueba:
- velnora / velnora  (administración)
- recepcion / recepcion  (recepción)
