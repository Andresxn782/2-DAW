# Resultado de aprendizaje 6

Alumno: Andres Ruiz · Módulo: Desarrollo web en entorno servidor · Unidad 6

**6. Desarrolla aplicaciones web de acceso a almacenes de datos, aplicando medidas para mantener la seguridad y la integridad de la información.**

## Criterios de evaluación

### a) Se han analizado las tecnologías que permiten el acceso mediante programación a la información disponible en almacenes de datos.

En `1.-Ejercicios/001-Establecimiento de conexiones/001-Acceso a datos.md` comparo las tres extensiones de PHP:

- `SQLite3` solo sirve para SQLite.
- `mysqli` solo sirve para MySQL.
- `PDO` sirve para casi cualquier motor, tiene consultas preparadas, excepciones y varios modos de recuperación.

He trabajado también con almacenes que no son bases de datos:

- imágenes (leo y escribo píxeles con GD, y guardo un texto dentro de una imagen en `010` y `011`);
- CSV (mi motor `VelnoraCSV`);
- JSON (`VelnoraJSON`);
- XML (SimpleXML, XPath y DOMDocument);
- archivos de registro y archivos `.ini`.

### b) Se han creado aplicaciones que establezcan conexiones con bases de datos.

- `012-conexion sqlite3.php`, `013-conexion pdo.php` (con `try/catch` y un error provocado) y `014-conexion mysql.php`. Este último conecta con MySQL de XAMPP, crea la base de datos `velnora` y copia los socios desde SQLite.
- `005-configuracion ini.php` saca los datos de conexión a un `.ini` y admite una variable de entorno para la contraseña.
- En el proyecto, `crearRepositorio()` abre la conexión que diga `config.php`: SQLite, MySQL o JSON.

### c) Se ha recuperado información almacenada en bases de datos.

En `003-Utilización de conjuntos de resultados/005-modos de recuperacion.php` recupero el mismo resultado de siete formas:

- `fetch` en bucle;
- `fetchAll`;
- `FETCH_OBJ`;
- `FETCH_CLASS` con mi clase `Socio`;
- `FETCH_KEY_PAIR`;
- `FETCH_COLUMN`;
- `fetchColumn`.

En el proyecto, `RepositorioPDO::buscar()` construye el `WHERE` con los filtros que haya, pagina con `LIMIT` y `OFFSET` y devuelve objetos `Entreno` con `FETCH_CLASS`.

### d) Se ha publicado en aplicaciones web la información recuperada.

- `006-agrupar resultados.php` pinta un gráfico de barras de socios por plan.
- `001-leer xml.php` muestra el horario de clases.
- `004-leer log.php` saca estadísticas del torno de entrada.
- Pulso `017` publica todas sus tablas.
- El proyecto muestra la tabla de entrenos paginada, cuatro indicadores (sesiones, horas, media y socio más constante) y un gráfico de minutos por tipo, todo calculado con los filtros elegidos.
- Todos los datos se escapan con `htmlspecialchars` antes de publicarlos.

### e) Se han utilizado conjuntos de datos para almacenar la información.

- En `003` guardo y cargo documentos JSON anidados (un socio con sus contactos y reservas) con `VelnoraJSON`, y explico en `001-Modelo de datos.md` la diferencia con las tablas relacionales.
- En `002` el motor `VelnoraCSV` usa carpetas como bases de datos y archivos CSV como tablas, y bloquea el archivo con `flock` al escribir.
- `RepositorioJSON` guarda todo el diario en un archivo JSON y hace en PHP lo que haría la base de datos: filtrar, ordenar, paginar y agrupar.

### f) Se han creado aplicaciones web que permitan la actualización y la eliminación de información disponible en una base de datos.

En `004-Actualización y eliminación...`:

- `001` edita un socio con `UPDATE` y muestra `rowCount()`.
- `002` borra solo por POST y después de confirmar. Si el socio tiene pagos, lo da de baja en lugar de borrarlo, para no perder la contabilidad (integridad).
- `003` cobra con una **transacción**: resta el saldo e inserta el pago, y si algo falla (saldo insuficiente, socio inexistente) hace `rollBack()` y no cambia nada.

En el proyecto:

- editar y borrar usan consultas preparadas y un token contra CSRF;
- el borrado pide confirmación;
- la importación de CSV es todo o nada: valida todas las líneas antes y guarda dentro de una transacción.

### g) Se han probado y documentado las aplicaciones web.

- `006-Prueba y documentación/001-pruebas VelnoraCSV.php` tiene 17 pruebas que pasan. Incluyen textos con comas, comillas y saltos de línea, y un intento de salir de la carpeta con `../`.
- El proyecto tiene `pruebas.php` con 47 pruebas que pasan. Lanzan las mismas comprobaciones contra SQLite, JSON y MySQL para demostrar que los tres repositorios se comportan igual, y prueban también la validación y que exportar e importar devuelve los mismos datos.
- Todas las clases están documentadas con PHPDoc, y cada carpeta y el proyecto tienen su explicación en Markdown.
- Además probé la aplicación completa a través del servidor con los tres almacenes: filtros, paginación, alta, edición, borrado, importación y exportación.
