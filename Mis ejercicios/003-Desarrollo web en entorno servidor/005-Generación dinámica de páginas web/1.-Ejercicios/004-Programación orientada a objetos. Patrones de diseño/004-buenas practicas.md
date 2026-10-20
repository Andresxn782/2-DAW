# Buenas prácticas de nombres

| Qué | Cómo lo escribo | Ejemplo |
|---|---|---|
| Variables | Minúsculas, palabras separadas con mayúscula o guion bajo, siempre igual | `$precioMensual` o `$precio_mensual` |
| Funciones y métodos | Verbo + nombre en camelCase | `dameDatos()`, `calcularCuota()` |
| Clases | Primera letra en mayúscula (PascalCase) | `VelnoraModelo` |
| Constantes | Todo en mayúsculas | `IVA_GENERAL` |
| Archivos de clases | Nombre de la clase o de su papel | `modelo.php`, `Clases/vista.php` |

Además:

- Un archivo, una responsabilidad.
- Nombres que digan qué hacen: `$s` no dice nada, `$socio` sí.
- Nada de valores sueltos repetidos por el código: van a una constante o a `config.php`.
