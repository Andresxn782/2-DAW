# Resultado de aprendizaje 2

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 2

**2. Escribe sentencias ejecutables por un servidor web reconociendo y aplicando procedimientos de integración del código en lenguajes de marcas.**

## Criterios de evaluación

### a) Se han reconocido los mecanismos de generación de páginas web a partir de lenguajes de marcas con código embebido.

Una página PHP es un documento HTML con bloques de código dentro. El servidor recorre el archivo, deja el HTML como está y sustituye cada bloque PHP por lo que imprime. El navegador recibe solo HTML. En la ficha del socio (`2.-Proyecto/velnora-ficha-socio/index.php`), los datos de la tarjeta se escriben con PHP dentro de etiquetas HTML normales.

Dónde lo he trabajado: `1.-Ejercicios/002-Obtención del lenguaje de marcas para mostrar en el cliente/000-Introducción.md` y `1.-Ejercicios/003-Etiquetas para inserción de código/001-web.php`.

### b) Se han identificado las principales tecnologías asociadas.

Las tecnologías que intervienen son el servidor web Apache, el intérprete de PHP con su archivo de configuración `php.ini`, y el navegador. Las he comprobado instalando XAMPP y abriendo `phpinfo()`, que muestra la versión de PHP, los módulos cargados y la configuración.

Dónde lo he trabajado: `1.-Ejercicios/001-Tecnologías asociadas/` (`001-Comprobación previa.md`, `002-phpinfo.php`, `003-configuración de apache.md`, `004-Configuracion de PHP.md`).

### c) Se han utilizado etiquetas para la inclusión de código en el lenguaje de marcas.

He usado la etiqueta completa `<?php ... ?>` para bloques de código y la etiqueta corta de impresión `<?= ... ?>` para escribir un valor. En el proyecto, toda la parte HTML usa `<?= ?>` para mostrar los datos del socio.

Dónde lo he trabajado: `1.-Ejercicios/003-Etiquetas para inserción de código/002-etiqueta.php` y `004-comando echo.php`.

### d) Se ha reconocido la sintaxis del lenguaje de programación que se ha de utilizar.

En PHP las variables empiezan por `$`, cada sentencia termina en `;`, los bloques van entre llaves y no se puede escribir texto suelto dentro de `<?php ?>`. En `003-error.php` provoco a propósito un error de sintaxis para ver el mensaje que da PHP y en qué línea lo marca.

Dónde lo he trabajado: `1.-Ejercicios/003-Etiquetas para inserción de código/003-error.php`.

### e) Se han escrito sentencias simples y se han comprobado sus efectos en el documento resultante.

He escrito sentencias `echo`, asignaciones y operaciones, y he comprobado en el navegador (y en "Ver código fuente") el HTML que generan. En la ficha del socio, cada cálculo de la cuota aparece en una tarjeta y he cambiado de socio con `?socio=2` para ver cómo cambia el resultado.

Dónde lo he trabajado: `1.-Ejercicios/003-Etiquetas para inserción de código/004-comando echo.php` y `2.-Proyecto/velnora-ficha-socio/index.php`.

### f) Se han utilizado directivas para modificar el comportamiento predeterminado.

He usado tres tipos de directivas:

- En `php.ini`, como `display_errors` y `date.timezone`.
- En tiempo de ejecución, con `ini_set('display_errors', '1')` y `error_reporting(E_ALL)` al principio del proyecto.
- Con `declare(strict_types=1)`, que hace que PHP no convierta los tipos al llamar a `calcularCuota()`: si le paso un texto en lugar de un número, da error.

Dónde lo he trabajado: `1.-Ejercicios/001-Tecnologías asociadas/004-Configuracion de PHP.md` y las primeras líneas de `2.-Proyecto/velnora-ficha-socio/index.php`.

### g) Se han utilizado los distintos tipos de variables y operadores disponibles en el lenguaje.

En el proyecto uso variables de todos los tipos:

- entero (`$edad`), decimal (`$precio`), cadena (`$nombre`), booleano (`$estudiante`), array (`$socios`) y null (`lesion`);
- conversiones explícitas (`(int)`, `(float)`, `intval`, `settype`) e implícitas (`"45" + 15`);
- operadores aritméticos (`*`, `-`, `%`), de comparación (`<`, `>=`, `!==`), lógicos (`||`, `&&`, `!`), de asignación (`+=`), de concatenación y de fusión con null (`??`).

La propia ficha muestra cada operación junto a su resultado.

Dónde lo he trabajado: `1.-Ejercicios/004-Tipos de datos. Conversiones entre tipos de datos/` y `1.-Ejercicios/005-Variables. Operadores. Ámbitos de utilización/` (`001-variables.php`, `003-operadores.php`, `004-comparacion.php`, `005-booleanos.php`).

### h) Se han identificado los ámbitos de utilización de las variables.

Una variable creada fuera de una función es global y no se ve dentro de la función a no ser que se use `global`. Una variable creada dentro de una función es local y desaparece al terminar. Con `static` la variable local conserva su valor entre llamadas. En el proyecto, `$ivaGimnasio` es global, `$descuento` es local de `calcularCuota()` y `$visitas` es `static` en `contarVisitaFicha()`, que después de 3 llamadas vale 3.

Dónde lo he trabajado: `1.-Ejercicios/005-Variables. Operadores. Ámbitos de utilización/` y `2.-Proyecto/velnora-ficha-socio/index.php`.
