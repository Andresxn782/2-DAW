# Mecanismos de generación dinámica de la interface web

La apariencia también puede salir de datos. Guardo el color, el nombre y el logo del gimnasio en un archivo de configuración, y la página los usa:

- `001-estilo dinamico`: PHP escribe el color dentro de una variable CSS al generar la página.
- `002-estilo dinamico`: la página pide la configuración en JSON con `fetch` y cambia la variable CSS desde JavaScript.

Así, para cambiar la imagen de la web, solo hay que tocar `config.php`.
