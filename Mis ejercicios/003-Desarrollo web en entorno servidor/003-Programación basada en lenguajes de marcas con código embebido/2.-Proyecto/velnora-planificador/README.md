# Proyecto de la unidad 3: Planificador de entrenamiento

Proyecto resumen de la unidad, hecho por Andres Ruiz para el gimnasio Velnora.

El socio rellena un formulario con su nombre, objetivo, nivel y días disponibles, y la página le genera un plan semanal. Cada plan se guarda en `planes.db` (SQLite) y abajo aparece el historial.

Qué usa de la unidad:

| Contenido | Dónde |
|---|---|
| Decisiones (`if`, `else if`, `switch`) | Validación del formulario, series según nivel, consejo según objetivo |
| Bucles (`for`, `foreach`, `while`) | Generar los días del plan y leer el historial |
| Arrays simples y multidimensionales | Catálogo de ejercicios por objetivo y grupo muscular |
| Funciones con parámetros y `return` | `seriesPorNivel()`, `generarPlan()`, `limpiar()` |
| Formularios GET y POST | Formulario de alta (POST) y filtro del historial (GET) |
| Recuperar datos del cliente | `$_POST`, `$_GET`, `$_SERVER['REQUEST_METHOD']` |
| Comentarios | De una línea, multilínea y de documentación de funciones |

## Cómo probarlo

Abrir `index.php` en XAMPP. La base de datos `planes.db` se crea sola la primera vez.
