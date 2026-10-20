# Proyecto de la unidad 6: Velnora Diario

Proyecto resumen de la unidad, hecho por Andres Ruiz. Es el diario de entrenamiento de los socios del gimnasio Velnora: cada socio apunta sus sesiones (tipo, minutos, intensidad y notas) y la aplicación las filtra, pagina, resume con estadísticas, importa desde CSV y exporta a CSV, JSON o XML.

En clase pasamos Pulso de SQLite a CSV cambiando solo el modelo. Este proyecto lleva esa idea más lejos: los datos pueden estar en **SQLite, MySQL o un archivo JSON**, y se cambia con una sola línea de `config.php`.

## Estructura

| Archivo | Papel |
|---|---|
| `config.php` | Elige el almacén (`sqlite`, `mysql` o `json`) y sus datos de conexión |
| `Clases/Entreno.php` | Una sesión de entrenamiento: validación y cálculo de calorías |
| `Clases/RepositorioEntrenos.php` | Interfaz con las operaciones que debe tener cualquier almacén (patrón Repositorio) |
| `Clases/RepositorioPDO.php` | Implementación con PDO, sirve para SQLite y para MySQL |
| `Clases/RepositorioJSON.php` | Implementación con un archivo JSON, sin base de datos |
| `Clases/Exportador.php` | Exporta a CSV, JSON y XML, y lee CSV para importar |
| `includes/inicio.php` | Crea el repositorio según la configuración (patrón factoría) |
| `index.php` | Listado con filtros, paginación y estadísticas |
| `editar.php`, `borrar.php` | Alta, edición y borrado con confirmación |
| `importar.php`, `exportar.php` | Subida de un CSV y descarga en tres formatos |
| `pruebas.php` | 47 pruebas: las mismas sobre SQLite, JSON y MySQL |

## Cómo probarlo

1. Copiar la carpeta en `htdocs` y abrir `index.php`. La primera vez carga 46 entrenos de `ejemplo.csv`.
2. Filtrar por socio, tipo y fechas, y ver cómo cambian las estadísticas.
3. Apuntar, editar y borrar entrenos.
4. Exportar a CSV, editar el archivo e importarlo. Si una línea está mal, no se importa nada.
5. Cambiar `'almacen'` en `config.php` a `'json'`, o a `'mysql'` con MySQL de XAMPP arrancado y la base de datos `velnora` creada (la crea `1.-Ejercicios/001-Establecimiento de conexiones/014-conexion mysql.php`).
6. Pruebas: `php pruebas.php`.
