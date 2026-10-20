Pulso con CSV · gimnasio Velnora (versión 017)
Autor: Andres Ruiz

Igual que la 016, pero los datos ya no están en SQLite: cada tabla es un archivo datos/gimnasio/<tabla>.csv,
gestionado por mi motor Clases/VelnoraCSV.php.

Solo ha cambiado Clases/modelo.php. El controlador y la vista son exactamente los de la 016:
el modelo tiene los mismos métodos, así que el resto de la aplicación no nota el cambio.

Pasos:
1. Ejecutar una vez importar_ejemplo.php: copia las tablas de gimnasio.db a CSV.
2. Entrar en index.php con velnora / velnora (administración) o recepcion / recepcion.

Como un CSV no tiene esquema, las claves primarias se indican en config.php
y el siguiente id se calcula buscando el mayor.
