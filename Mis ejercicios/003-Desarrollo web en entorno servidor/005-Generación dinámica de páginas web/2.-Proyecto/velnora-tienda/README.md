# Proyecto de la unidad 5: Velnora Tienda

Proyecto resumen de la unidad, hecho por Andres Ruiz. Es la tienda online del gimnasio Velnora: catálogo con buscador, ficha de producto, carrito y una zona de administración para crear, editar y borrar productos.

En clase pasamos el panel de gestión a MVC (en mis ejercicios es Pulso, versiones 014 a 016). Este proyecto aplica lo mismo a una aplicación distinta y añade más patrones.

## Estructura

| Archivo | Papel |
|---|---|
| `index.php` | Controlador frontal: todas las peticiones entran por aquí |
| `config.php` | Configuración: nombre, color, IVA, gastos de envío, base de datos y acceso de administración |
| `Clases/BaseDatos.php` | Conexión PDO única (patrón Singleton). Crea la base de datos con productos de ejemplo |
| `Clases/Modelo.php` | Clase abstracta con el CRUD genérico |
| `Clases/Producto.php` | Modelo de productos (hereda de `Modelo`): campos, filtro y validación |
| `Clases/Carrito.php` | Carrito guardado en la sesión, con IVA y envío |
| `Clases/Vista.php` | Carga plantillas y genera los controles del formulario a partir de los campos del modelo |
| `Clases/Controlador.php` | Decide qué acción ejecutar según la ruta y protege la administración |
| `plantillas/` | HTML de cada página, con `diseno.php` como plantilla común |
| `pruebas.php` | 17 pruebas automáticas de los modelos |

## Cómo probarlo

1. Copiar la carpeta en `htdocs` y abrir `index.php`. La base de datos `tienda.db` se crea sola.
2. Buscar productos, añadir al carrito y cambiar cantidades. Desde 50 € el envío es gratis. La sudadera está agotada.
3. "Acceso tienda" con usuario `admin` y contraseña `velnora2026` para gestionar los productos.
4. Cambiar `color` en `config.php` para ver la tienda con otra imagen.
5. Pruebas: `php pruebas.php` en una terminal dentro de la carpeta.
