# Resultado de aprendizaje 5

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 5

**5. Desarrolla aplicaciones web identificando y aplicando mecanismos para separar el código de presentación de la lógica de negocio.**

## Criterios de evaluación

### a) Se han identificado las ventajas de separar la lógica de negocio de los aspectos de presentación de la aplicación.

Si todo está en el mismo archivo, como en `001-codigofeo.php` y `002-masfeo.php`, cualquier cambio de diseño obliga a tocar las consultas y al revés. Separando:

- se puede cambiar el aspecto sin tocar los datos (la misma vista pinta socios y material en `005`);
- el código se reutiliza (el modelo de Pulso sirve para todas sus tablas);
- se puede probar la lógica sin navegador (`pruebas.php` de la tienda);
- varias personas pueden trabajar a la vez, una en el HTML y otra en la lógica.

Dónde lo he trabajado: `1.-Ejercicios/001-Mecanismos de separación de la lógica de negocio. Frameworks web servidor/`.

### b) Se han analizado y utilizado mecanismos y frameworks que permiten realizar esta separación y sus características principales.

He separado de tres formas:

1. El servidor devuelve JSON y el cliente lo pinta con `fetch` (`003-separacion por capas`).
2. Archivos PHP distintos para datos y vista (`004` y `005`).
3. Modelo, vista y controlador (`006`, `007` y Pulso `015`).

Los frameworks como Laravel o Symfony traen esta estructura hecha: rutas, controladores, modelos (Eloquent, Doctrine) y plantillas (Blade, Twig). En la tienda he hecho mi propia versión pequeña: un controlador frontal con rutas, modelos con herencia y plantillas en `plantillas/`.

### c) Se han utilizado objetos y controles en el servidor para generar el aspecto visual de la aplicación web en el cliente.

En `1.-Ejercicios/002-Controles de servidor/controles.php` hay funciones que generan desplegables, casillas y botones de opción a partir de arrays o de la base de datos, y que recuerdan lo que eligió el usuario. Las clases `VelnoraVista` (ejercicios) y `Vista` (tienda) son objetos que generan todo el HTML: tablas, tarjetas, formularios y el menú según el rol.

### d) Se han utilizado formularios generados de forma dinámica para responder a los eventos de la aplicación web.

- En Pulso `015`, el formulario de alta y edición se genera con las columnas de cada tabla, y el tipo de cada campo (email, fecha, número) se elige según el nombre de la columna.
- En la tienda, `Producto::campos()` define los campos y `Vista::control()` construye cada control: texto, desplegable, área de texto, número o casilla. El mismo formulario sirve para crear y para editar.
- Si la validación falla, el formulario vuelve con los datos escritos y el error debajo de cada campo.
- Los botones "Añadir al carrito", "Actualizar cantidades" y "Borrar" lanzan cada uno su acción en el controlador.

### e) Se han identificado y aplicado los parámetros relativos a la configuración de la aplicación web.

- En `003-Mecanismos de generación dinámica de la interface web` el color y el nombre salen de `config.php`, en PHP (`001`) o pidiéndolos en JSON con JavaScript (`002`).
- En Pulso `016`, `config.php` guarda nombre, logo, color y base de datos, y `leeconfiguracion.php` solo expone la parte visual.
- En la tienda, `config.php` tiene el IVA, los gastos de envío, el importe para envío gratis, la moneda, el acceso de administración (con la contraseña cifrada) y el modo de depuración.

### f) Se han escrito aplicaciones web con mantenimiento de estado y separación de la lógica de negocio.

- Pulso `015` y `016` mantienen la sesión del usuario y su rol en el controlador.
- En la tienda, el carrito vive en `$_SESSION['carrito']` y lo gestiona la clase `Carrito`.
- También en la tienda, la sesión de administración se crea con `session_regenerate_id`, y los avisos se guardan en la sesión para mostrarlos después de redirigir.

Ninguna de las dos mezcla el estado con el HTML: la vista solo recibe los datos.

### g) Se han aplicado los principios y patrones de diseño de la programación orientada a objetos.

He usado:

- clases con propiedades privadas y constructor (`007-correccion del objeto.php`);
- métodos estáticos (`009`);
- herencia y clases abstractas (`Producto extends Modelo`);
- encapsulación (el carrito esconde cómo guarda los datos);
- inyección de dependencias (el controlador recibe el modelo, el carrito y la vista).

En cuanto a patrones: **MVC** (Pulso y tienda), **Singleton** (`BaseDatos::conexion()`) y **controlador frontal** (`index.php` de la tienda). Los nombres siguen las buenas prácticas de `004-buenas practicas.md`.

### h) Se ha probado y documentado el código.

- `1.-Ejercicios/005-Prueba y documentación del código/` tiene la clase `Cuota` documentada con PHPDoc (`@param`, `@return`, `@throws`) y 8 pruebas que pasan.
- La tienda está documentada con PHPDoc en todas sus clases y tiene `pruebas.php`, con 17 pruebas que pasan. Comprueban el filtro, la validación, que no se guardan campos extraños, el stock, el IVA y el envío gratis.
- También probé la tienda y Pulso `015` y `016` completos a través del servidor: login, permisos, alta, edición, borrado y carrito.
