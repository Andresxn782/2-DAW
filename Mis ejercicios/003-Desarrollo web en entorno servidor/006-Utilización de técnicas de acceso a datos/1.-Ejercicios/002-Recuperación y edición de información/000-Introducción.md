# Recuperación y edición de información

Un archivo CSV también puede hacer de base de datos. Primero lo leo, después lo meto en una clase, y al final construyo un pequeño motor (`VelnoraCSV`) con las operaciones de una base de datos: crear base de datos (carpeta), crear tabla (archivo), insertar, buscar, actualizar y eliminar.

Para terminar, cambio el modelo de Pulso para que guarde todo en CSV en lugar de SQLite (`017-pulso con CSV`). El controlador y la vista no cambian: esa es la ventaja del MVC.
