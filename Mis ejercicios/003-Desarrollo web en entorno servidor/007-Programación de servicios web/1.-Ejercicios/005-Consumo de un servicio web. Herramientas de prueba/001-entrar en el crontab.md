# Programar la descarga

## En Linux: crontab

```
crontab -e
```

Y añado esta línea (cada hora en punto, ejecuta el script con PHP de consola):

```
0 * * * * php "/ruta/a/005-Consumo de un servicio web. Herramientas de prueba/002-guardar.php" >> /tmp/velnora-aemet.log 2>&1
```

Los cinco campos son: minuto, hora, día del mes, mes y día de la semana. `*` significa "cualquiera".

## En Windows (mi XAMPP): Programador de tareas

1. Abrir "Programador de tareas" y "Crear tarea básica".
2. Desencadenador: diariamente, y en opciones avanzadas "repetir cada 1 hora".
3. Acción: iniciar un programa.
   - Programa: `C:\xampp\php\php.exe`
   - Argumentos: `"C:\xaampp\htdocs\2-DAW\Mis ejercicios\003-Desarrollo web en entorno servidor\007-Programación de servicios web\1.-Ejercicios\005-Consumo de un servicio web. Herramientas de prueba\002-guardar.php"`

Para probarlo sin esperar, se ejecuta a mano en la terminal:

```
php "002-guardar.php"
```
