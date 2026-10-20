# Resultado de aprendizaje 1

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 1

**1. Selecciona las arquitecturas y tecnologías de programación web en entorno servidor, analizando sus capacidades y características propias.**

## Criterios de evaluación

### a) Se han caracterizado y diferenciado los modelos de ejecución de código en el servidor y en el cliente web.

En el modelo cliente-servidor, el navegador (cliente) pide una página y el servidor la devuelve. El código de cliente (HTML, CSS y JavaScript) se descarga y se ejecuta en el ordenador del usuario, así que cualquiera puede verlo. El código de servidor (PHP) se ejecuta en el servidor antes de enviar la respuesta, y el usuario solo recibe el resultado.

En mi proyecto `2.-Proyecto/velnora-donde-se-ejecuta/index.php` se ven los dos modelos a la vez: la hora de PHP se queda fija porque se calculó en el servidor, y la hora de JavaScript avanza cada segundo porque se ejecuta en el navegador.

Dónde lo he trabajado: `1.-Ejercicios/001-Modelos de ejecución de código en entornos clienteservidor/001-modelos.md`.

### b) Se han reconocido las ventajas que proporciona la generación dinámica de páginas.

Con páginas dinámicas, una sola plantilla sirve para muchos contenidos, los datos salen de una base de datos y se actualizan sin tocar el HTML, y cada usuario puede ver información personalizada. En el proyecto, el horario del gimnasio se genera con un `foreach` y el día de hoy se marca solo.

Dónde lo he trabajado: `1.-Ejercicios/002-Generación dinámica de páginas web/001-ejemplo.php` (página estática) frente a `002-ejemplo bueno.php` (la misma página generada con PHP), y el apartado 3 de `2.-Proyecto/velnora-donde-se-ejecuta/informe.md`.

### c) Se han identificado los mecanismos de ejecución de código en los servidores web.

Cuando Apache recibe una petición de un archivo `.php`, no lo envía tal cual: se lo pasa al intérprete de PHP (en XAMPP, como módulo de Apache). PHP ejecuta el código y devuelve HTML, que Apache envía al navegador. Los archivos estáticos (HTML, imágenes, CSS) se envían directamente.

En el proyecto, la tabla "Quién ha atendido la petición" muestra el servidor (`SERVER_SOFTWARE`) y la interfaz entre Apache y PHP (`php_sapi_name()`).

Dónde lo he trabajado: `1.-Ejercicios/005-Integración con los servidores web/001-Que es un servidor web.md`.

### d) Se han reconocido las funcionalidades que aportan los servidores de aplicaciones y su integración con los servidores web.

El servidor web se encarga del protocolo HTTP, y el servidor de aplicaciones ejecuta la lógica: sesiones, acceso a bases de datos, seguridad. En PHP esa parte la hace el propio intérprete integrado en Apache. En otras tecnologías hay servidores de aplicaciones separados, como Tomcat para Java, al que Apache o Nginx le reenvían las peticiones.

Dónde lo he trabajado: `1.-Ejercicios/005-Integración con los servidores web/001-Que es un servidor web.md` y el apartado 4 de `2.-Proyecto/velnora-donde-se-ejecuta/informe.md`.

### e) Se han identificado y caracterizado los principales lenguajes y tecnologías relacionados con la programación web en entorno servidor.

Los principales son PHP, Python (Django, Flask), JavaScript con Node.js, Java (Spring), C# (ASP.NET) y Ruby (Rails). En el informe del proyecto los comparo con ventajas e inconvenientes, y elijo PHP para el gimnasio porque funciona en cualquier hosting y se integra directamente en el HTML.

Dónde lo he trabajado: `1.-Ejercicios/003-Lenguajes de programación y tecnologías asociadas en entorno servidor/001-Lenguajes para servidor.md` y el apartado 5 de `informe.md`.

### f) Se han verificado los mecanismos de integración de los lenguajes de marcas con los lenguajes de programación en entorno servidor.

PHP se mete dentro del HTML con las etiquetas `<?php ... ?>`. Lo he comprobado abriendo el proyecto en el navegador y mirando el código fuente: los bloques PHP han desaparecido y en su lugar está el HTML que generaron, mientras que el `<script>` de JavaScript aparece completo.

Dónde lo he trabajado: `1.-Ejercicios/004-Integración con los lenguajes de marcas/001-que son los lenguajes de marcas.md` y `2.-Proyecto/velnora-donde-se-ejecuta/index.php`.

### g) Se han reconocido y evaluado las herramientas y frameworks de programación en entorno servidor.

Uso XAMPP (Apache, PHP y base de datos), VS Code como editor, las herramientas de desarrollo del navegador (F12) y Git con GitHub. Entre los frameworks he visto Laravel y Symfony para PHP, Django para Python y Express para Node.js. Para este curso empiezo sin framework para entender cada paso, y en el informe explico cuándo merecería la pena usar uno.

Dónde lo he trabajado: `1.-Ejercicios/006-Herramientas y frameworks de programación. en entorno servidor/001-Herramientas.md`, `002-Frameworks.md` y el apartado 7 de `informe.md`.
