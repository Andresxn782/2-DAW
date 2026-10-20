# Repaso de programación orientada a objetos

- Una **clase** es un molde: describe cómo es algo. Por ejemplo, `Socio`.
- Un **objeto** es una instancia de la clase: `$lucia = new Socio("Lucía")`.
- Las clases tienen **propiedades** (variables: `$nombre`, `$plan`) y **métodos** (funciones: `pagarCuota()`).
- El **constructor** (`__construct`) se ejecuta al crear el objeto y prepara sus propiedades.
- **Visibilidad**: `public` se usa desde fuera, `private` solo desde dentro de la clase, `protected` desde la clase y sus hijas.
- **Encapsular**: el objeto esconde cómo funciona por dentro y solo enseña los métodos que hacen falta.
- **Reutilizar**: la misma clase sirve en muchos programas, y con la **herencia** (`extends`) una clase hija aprovecha lo de la madre.
- Un **método estático** (`static`) se llama sobre la clase, sin crear objeto: `Clase::metodo()`.

## Patrones de diseño que uso

- **MVC (modelo-vista-controlador)**: el modelo accede a los datos, la vista genera el HTML y el controlador recibe la petición y decide qué hacer.
- **Inyección de dependencias**: el controlador recibe el modelo en su constructor en lugar de crearlo él.
- **Front controller**: todas las peticiones entran por `index.php`.
