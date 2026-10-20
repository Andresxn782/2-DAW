# Mecanismos de separación de la lógica de negocio. Frameworks web servidor

Si mezclo en un mismo archivo la conexión a la base de datos, las consultas y el HTML, el código funciona pero es difícil de mantener. En estos ejercicios voy separando el listado de socios del gimnasio en capas:

1. `001` y `002`: todo junto (lo que no hay que hacer).
2. `003`: el servidor solo devuelve datos en JSON y el HTML los pinta con JavaScript.
3. `004` y `005`: separación en PHP, con un archivo para los datos y otro para pintar.
4. `006` y `007`: tres capas, modelo (datos), vista (HTML) y controlador (une los dos).

Los frameworks de servidor (Laravel, Symfony, CodeIgniter) ya traen esta separación hecha: rutas, controladores, modelos y plantillas.
