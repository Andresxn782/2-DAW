# Resultado de aprendizaje 3

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 3

**3. Escribe bloques de sentencias embebidos en lenguajes de marcas, seleccionando y utilizando las estructuras de programación.**

## Criterios de evaluación

### a) Se han utilizado mecanismos de decisión en la creación de bloques de sentencias.

He usado `if`, `else`, `else if` y `switch`. En los ejercicios clasifico las zonas de frecuencia cardiaca y elijo la rutina según el día. En el planificador (`2.-Proyecto/velnora-planificador/index.php`):

- valido el formulario con `if` / `else if`;
- elijo las series según el nivel con un `switch` en `seriesPorNivel()`;
- muestro un consejo distinto según el objetivo.

Dónde lo he trabajado: `1.-Ejercicios/001-Tomas de decisión/` (`001-if.php`, `002-else.php`, `003-else if.php`, `004-switch.php`).

### b) Se han utilizado bucles y se ha verificado su funcionamiento.

He usado:

- `for` para el reto de 30 días;
- bucles embebidos en el HTML;
- un doble `for` para un calendario de 12 semanas por 7 días;
- un calendario de 2026 que marca los días de entrenamiento y el día de hoy.

En el proyecto, `generarPlan()` reparte los grupos musculares con un `for`, los días y ejercicios se pintan con `foreach`, y el historial se lee con `while`. He comprobado que el reparto rota bien: con 4 días de fuerza salen Pierna, Empuje, Tirón y Pierna.

Dónde lo he trabajado: `1.-Ejercicios/002-Bucles/` (`001-for.php` a `004-un poco de ia.php`).

### c) Se han utilizado matrices (arrays) para almacenar y recuperar conjuntos de datos.

He usado arrays simples (lista de ejercicios), asociativos (datos de un socio) y multidimensionales (lista de socios con sus datos). El catálogo del planificador es un array de tres niveles (objetivo → grupo → ejercicios) y uso `array_keys`, `in_array`, `array_intersect` e `implode` para trabajar con él.

Dónde lo he trabajado: `1.-Ejercicios/003-Matrices (arrays)/001-array unidimensional.php`, `002-arrays multidimensionales.php` y `1.-Ejercicios/004-Tipos de datos compuestos/`.

### d) Se han creado y utilizado funciones.

He creado funciones sin parámetros (`bienvenida()`), con parámetros (`bienvenida($nombre, $plan)`) y con `return` (`calcularIMC`). En el proyecto hay tres funciones propias: `seriesPorNivel($nivel)`, `generarPlan($catalogo, $objetivo, $dias)` y `limpiar($texto)`.

Dónde lo he trabajado: `1.-Ejercicios/005-Funciones/` (`001-funcion.php`, `002-funciones con parametros.php`, `003-return.php`).

### e) Se han utilizado formularios web para interactuar con el usuario del navegador web.

El planificador tiene un formulario con campo de texto, dos `select` y casillas (`dias[]`) que se envían por POST. Si hay errores, el formulario conserva lo que había escrito el usuario. El historial tiene enlaces de filtro que se envían por GET.

Dónde lo he trabajado: `1.-Ejercicios/007-Procesamiento de la información introducida en un formulario/` (`001-dos formas get.html`, `002-post.html`, `009-formulario.php`, `010-formulario.php` y `018-todo junto.php`).

### f) Se han empleado métodos para recuperar la información introducida en el formulario.

Recupero los datos con `$_POST`, `$_GET` y `$_REQUEST`, compruebo el método con `$_SERVER['REQUEST_METHOD']` y uso `??` cuando un campo puede no llegar. Antes de usar los datos los limpio con `trim` y `htmlspecialchars`, solo acepto valores de mis listas y los guardo en SQLite con consultas preparadas.

Dónde lo he trabajado: `1.-Ejercicios/006-Recuperación y utilización de información proveniente del cliente web/` y `1.-Ejercicios/007-Procesamiento de la información introducida en un formulario/` (`003` a `006`, y `011-insertar.php`).

### g) Se han añadido comentarios al código.

Uso comentarios de una línea (`//`) para explicar pasos, multilínea (`/* */`) para la cabecera del archivo, y de documentación (`/** */`) encima de cada función para decir qué hace y qué devuelve. Los comentarios explican el porqué, no repiten el código.

Dónde lo he trabajado: `1.-Ejercicios/008-Comentarios/` (`001` a `003`) y la cabecera de `2.-Proyecto/velnora-planificador/index.php`.
