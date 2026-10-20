# Proyecto de la unidad 1: ¿Dónde se ejecuta?

Proyecto resumen de la unidad, hecho por Andres Ruiz para el gimnasio Velnora.

Es una pequeña web que enseña, con datos reales, qué parte de una página la genera el servidor y qué parte la ejecuta el navegador:

- `index.php` muestra a la vez la hora del servidor (PHP) y la hora del cliente (JavaScript), el horario del gimnasio generado dinámicamente y los datos del servidor web que atiende la petición.
- `informe.md` es el informe técnico en el que elijo la arquitectura y las herramientas para la futura web del gimnasio.

## Cómo probarlo

1. Copiar la carpeta dentro de `htdocs` de XAMPP.
2. Arrancar Apache.
3. Abrir `http://localhost/.../velnora-donde-se-ejecuta/index.php`.
4. Pulsar "Ver código fuente" en el navegador: el código PHP no aparece, solo su resultado. El JavaScript sí aparece, porque se ejecuta en el cliente.
