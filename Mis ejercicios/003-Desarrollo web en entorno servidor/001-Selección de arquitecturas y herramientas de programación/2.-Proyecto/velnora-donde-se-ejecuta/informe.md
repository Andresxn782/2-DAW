# Informe técnico: arquitectura para la web del gimnasio Velnora

Autor: Andres Ruiz

## 1. Necesidades

El gimnasio Velnora quiere una web donde:

- cualquier visitante vea el horario, las clases y los precios;
- los socios entren con usuario y contraseña y reserven clases;
- recepción gestione socios, pagos y reservas.

Los horarios, las clases y las plazas libres cambian cada día, así que la web no puede ser solo HTML estático: tiene que generarse en el servidor a partir de una base de datos.

## 2. Modelo de ejecución elegido

| Parte | Dónde se ejecuta | Para qué |
|---|---|---|
| HTML y CSS | Navegador (cliente) | Mostrar la página |
| JavaScript | Navegador (cliente) | Validar formularios antes de enviar, relojes, efectos |
| PHP | Servidor | Login, consultas a la base de datos, generar el HTML |
| SQLite (luego MySQL) | Servidor | Guardar socios, clases, reservas y pagos |

El cliente nunca ve el código PHP ni la base de datos, solo el HTML resultante. Así las contraseñas y los datos de los socios no salen del servidor.

## 3. Ventajas de generar las páginas dinámicamente

- Un solo archivo `clases.php` sirve para todas las clases, en lugar de una página HTML por clase.
- Cuando recepción cambia un horario en la base de datos, la web se actualiza sola.
- Cada socio ve su propia información (sus reservas, su plan) en la misma página.
- Se puede controlar quién entra a cada parte (socios, recepción, administración).

## 4. Servidor web y servidor de aplicaciones

- **Apache** (incluido en XAMPP) recibe las peticiones HTTP. Si el archivo es `.html` o una imagen, lo devuelve tal cual; si es `.php`, se lo pasa al módulo de PHP.
- **PHP** hace de servidor de aplicaciones: ejecuta el código, consulta la base de datos y devuelve HTML a Apache, que se lo envía al navegador.
- En el proyecto `index.php` se ve qué servidor y qué interfaz (`apache2handler`, `cli-server`…) atienden la petición.

## 5. Lenguajes de servidor que he comparado

| Lenguaje | Ventajas | Inconvenientes |
|---|---|---|
| PHP | Lo tiene cualquier hosting, muy fácil de empezar con XAMPP, se mezcla con HTML | Si no se ordena, el código se vuelve un lío |
| Python (Django/Flask) | Código muy legible, bueno para datos | Hosting menos común, hay que configurar más |
| JavaScript (Node.js) | Mismo lenguaje en cliente y servidor | Hay que montar el servidor uno mismo |
| Java (Spring) | Muy robusto para empresas grandes | Pesado para un gimnasio pequeño |

**Elección:** PHP, porque es el que usamos en clase, funciona en cualquier hosting barato y se integra directamente con el HTML.

## 6. Integración con el lenguaje de marcas

PHP se escribe dentro del HTML entre `<?php` y `?>`. El servidor sustituye cada bloque por lo que imprime, y el resto del HTML pasa sin cambios. En `index.php` el horario es una tabla HTML cuyas filas genera un `foreach` de PHP.

## 7. Herramientas y frameworks

| Herramienta | Uso en el proyecto |
|---|---|
| XAMPP | Apache + PHP + base de datos en local |
| VS Code | Editor con extensiones para PHP |
| Navegador (F12) | Ver el HTML generado y depurar JavaScript |
| Git y GitHub | Guardar versiones y entregar el código |
| Laravel / Symfony | Frameworks PHP que usaría si la web creciera mucho |

Para la primera versión no uso framework: así entiendo qué hace cada línea. Cuando la aplicación crezca, la pasaré a una estructura modelo-vista-controlador.
