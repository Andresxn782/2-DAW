# Prueba y documentación del código

- **Documentar**: cada clase y cada método llevan un comentario PHPDoc (`/** ... */`) con lo que hacen, sus parámetros (`@param`) y lo que devuelven (`@return`). Los editores como VS Code lo muestran al pasar el ratón, y herramientas como phpDocumentor generan una web de documentación a partir de estos comentarios.
- **Probar**: escribo pruebas que llaman a los métodos con datos conocidos y comprueban el resultado. Así, si cambio algo y rompo otra parte, me entero enseguida. En proyectos grandes se usa PHPUnit; aquí hago una versión sencilla con mi propia función `comprobar()`.

Archivos:

- `001-cuota documentada.php`: clase `Cuota` documentada con PHPDoc.
- `002-pruebas de la cuota.php`: pruebas de esa clase. Se ejecuta con `php "002-pruebas de la cuota.php"`.
