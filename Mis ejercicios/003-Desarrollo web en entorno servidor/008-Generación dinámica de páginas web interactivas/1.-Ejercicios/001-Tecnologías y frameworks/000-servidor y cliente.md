# Servidor y cliente

## Diferencias

| | Código de servidor (PHP) | Código de cliente (JavaScript) |
|---|---|---|
| Dónde se ejecuta | En el servidor, antes de enviar la página | En el navegador del usuario, después de recibirla |
| Qué ve el usuario | Solo el resultado (HTML, JSON) | El código entero (F12) |
| A qué tiene acceso | Base de datos, archivos, sesiones, claves | La página (DOM), el teclado, el ratón, `localStorage` |
| Cuándo se ejecuta | Una vez por petición | Todo el rato, respondiendo a eventos |
| Seguridad | Es de fiar: el usuario no lo puede cambiar | No es de fiar: el usuario lo puede modificar |

## Ventajas de unir los dos

- El servidor tiene los datos y las reglas; el cliente da la respuesta inmediata.
- Con `fetch` el cliente pide al servidor solo lo que necesita y cambia un trozo de la página, sin recargarla entera. Se gasta menos red y se nota más rápido.
- La validación se hace en los dos lados: en el cliente para avisar al momento y en el servidor porque es la que cuenta.
- El servidor puede mandar la primera versión de la página ya rellena (rápida y buena para buscadores) y luego el cliente la mantiene al día.

## Tecnologías y frameworks que insertan código en el HTML

- **Servidor con guiones embebidos**: PHP (`<?php ?>` y `<?= ?>`), JSP en Java, ASP.NET (Razor, `@`), plantillas como Twig o Blade en Laravel, Jinja en Python.
- **Cliente**: JavaScript puro con el DOM y `fetch`, y frameworks como React, Vue o Angular, que también mezclan marcas y código (JSX, `{{ }}`).
- **Mixtos**: Livewire o htmx, en los que el servidor devuelve trozos de HTML que el cliente coloca en la página.

En esta unidad uso PHP en el servidor y JavaScript sin frameworks en el cliente, para ver bien qué hace cada parte.
