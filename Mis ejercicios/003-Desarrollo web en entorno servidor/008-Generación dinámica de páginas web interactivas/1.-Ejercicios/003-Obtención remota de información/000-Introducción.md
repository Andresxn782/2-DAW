# Obtención remota de información

Un formulario de alta de socios que pregunta al servidor mientras se rellena:

- `001-comprobar email.php` dice si un email ya está dado de alta.
- `002-validar.php` valida todos los campos en el servidor y, si están bien, guarda el socio.
- `003-alta de socio.html` valida en el navegador campo a campo, consulta el email al servidor mientras se escribe y envía el formulario con `fetch`, sin recargar. Los errores que devuelve el servidor se colocan debajo de cada campo.

Con la casilla "Saltar la validación del navegador" se ve que, aunque el cliente no valide, el servidor no deja pasar datos malos.
