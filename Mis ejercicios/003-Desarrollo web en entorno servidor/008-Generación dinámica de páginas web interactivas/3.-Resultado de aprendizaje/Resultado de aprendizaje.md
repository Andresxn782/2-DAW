# Resultado de aprendizaje 8

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 8

**8. Genera páginas web dinámicas analizando y utilizando tecnologías y frameworks del servidor web que añadan código al lenguaje de marcas.**

## Criterios de evaluación

### a) Se han identificado las diferencias entre la ejecución de código en el servidor y en el cliente web.

En `1.-Ejercicios/001-Tecnologías y frameworks/000-servidor y cliente.md` comparo los dos lados: dónde se ejecuta cada uno, qué puede ver el usuario, a qué tiene acceso, cuándo se ejecuta y si es de fiar.

Con los ejercicios se ve en la práctica:

- `001-interactivo.html` (calculadora de IMC) se ejecuta entero en el navegador, sin pedir nada al servidor;
- `003-php conecto.php` se ejecuta entero en el servidor, y el navegador solo recibe el HTML final;
- `005-datalist interactivo.html` mezcla los dos: el navegador pide y el servidor busca en la base de datos.

### b) Se han reconocido las ventajas de unir ambas tecnologías en el proceso de desarrollo de programas.

- El servidor guarda los datos y las reglas, y el cliente responde al momento sin recargar la página. El chat del apartado 002 y la pizarra del proyecto se actualizan solos.
- Se gasta menos red. En el paso 008 del chat y en la pizarra solo se piden los mensajes nuevos (`desde`, `animos_desde`), no todo cada vez.
- La validación doble: el cliente avisa antes de enviar y el servidor no deja pasar nada malo aunque se salte la del cliente. En el alta de socio hay una casilla para comprobarlo.
- En `002-horario.php` y en la pizarra, la primera carga la hace el servidor (rápida y funciona sin JavaScript) y luego JavaScript mantiene la página al día.

### c) Se han identificado las tecnologías y frameworks relacionados con la generación por parte del servidor de páginas web con guiones embebidos.

- En `000-servidor y cliente.md` repaso PHP (`<?php ?>`, `<?= ?>`), JSP, ASP.NET Razor, plantillas como Twig, Blade o Jinja, los frameworks de cliente (React, Vue, Angular) y los mixtos como htmx o Livewire.
- En mis páginas uso PHP embebido en el HTML (`index.php` de la pizarra, `002-horario.php`) y también el servidor como generador de fragmentos (`clases.php?formato=html`).

### d) Se han utilizado estas tecnologías para generar páginas web que incluyan interacción con el usuario.

- El buscador de ejercicios sugiere mientras se escribe, desde la base de datos `ejercicios.db` con 58 ejercicios.
- La rutina se monta añadiendo, quitando, moviendo y marcando ejercicios.
- El horario cambia de día con botones.
- Velnora Sala es un chat que se construye en ocho pasos, desde la pantalla de entrada hasta enviar y recibir en directo.
- En la pizarra del proyecto se apuntan marcas y ánimos, y el monitor publica WODs.

### e) Se han utilizado estas tecnologías, para generar páginas web que incluyan verificación de formularios.

- `003-Obtención remota de información/003-alta de socio.html` valida campo a campo en el navegador:
  - restricciones de HTML5 y `setCustomValidity` para la edad mínima;
  - el email se comprueba contra el servidor mientras se escribe (`001-comprobar email.php`).
- `002-validar.php` repite todas las comprobaciones en el servidor y devuelve un 422 con un error por campo, que el cliente coloca debajo de cada caja.
- En la pizarra, `includes/valores.php` y `validarValor()` de `pizarra.js` aplican la misma regla, que cambia según el WOD (`m:ss` o repeticiones). Si se envía algo mal, el servidor responde 422.
- El servidor del chat rechaza los mensajes vacíos o de más de 200 caracteres.

### f) Se han utilizado estas tecnologías para generar páginas web que incluyan modificación dinámica de su contenido y su estructura.

- `001-rutina.html` crea y borra elementos (`createElement`, `remove`), los reordena (`insertBefore`), cambia clases (`classList.toggle`) y recalcula los totales.
- `002-horario.php` cambia un trozo de la página de dos formas: con HTML hecho por el servidor (`innerHTML`) o con JSON y una `<template>` clonada.
- En la pizarra:
  - la clasificación reutiliza las filas de cada socio, las mueve a su nuevo puesto con `appendChild`, quita las que sobran y destaca las que cambian;
  - cuando hay WOD nuevo cambian la etiqueta y el tipo del campo, se vacían la tabla y los ánimos, y se anima la cabecera.

### g) Se han aplicado estas tecnologías en la programación de aplicaciones web.

El proyecto `2.-Proyecto/velnora-pizarra` es una aplicación completa:

- entrada con sesión y dos roles (socio y monitor con PIN), con `session_regenerate_id`;
- página generada por el servidor y actualizada por el cliente cada 2 segundos;
- API JSON con token contra CSRF en la cabecera `X-Token` y códigos de estado;
- SQLite con `ON CONFLICT` para que cada socio tenga una sola marca por WOD;
- clasificación con los RX delante de los escalados;
- todo el texto de los usuarios escapado: `htmlspecialchars` en PHP y `textContent` en JavaScript;
- `pruebas.php` con 38 comprobaciones, y probado en el navegador con un socio y un monitor a la vez.
