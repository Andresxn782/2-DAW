# Utilización de otros orígenes de datos

Además de bases de datos, CSV y JSON, una aplicación web puede leer datos de:

- **XML**: `001-leer xml.php` lee el horario de clases de `clases.xml` con SimpleXML y lo filtra con XPath.
- **Escribir XML**: `002-exportar xml.php` genera un XML con los socios de la base de datos.
- **Exportar a CSV para Excel**: `003-exportar csv.php` descarga los socios en un CSV que Excel abre bien (con BOM y punto y coma).
- **Archivos de texto con un formato propio**: `004-leer log.php` lee el registro de accesos del torno de la entrada y saca estadísticas.
- **Variables de entorno y archivos .ini**: `005-configuracion ini.php` lee la configuración de la conexión de un `.ini`, que es como se suele separar del código.
