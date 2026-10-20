# Velnora Pizarra

Proyecto de la unidad 8 (Generación dinámica de páginas web interactivas). Andres Ruiz.

Es la pizarra del box de crossfit de Velnora, pero en la pantalla:

- el monitor publica el WOD (el entrenamiento del día), que puede ser **por tiempo** o **por repeticiones**;
- cada socio apunta su marca desde el móvil, y puede marcarla como escalada;
- la clasificación se actualiza sola en todas las pantallas, con las filas que cambian iluminadas;
- hay un muro de ánimos para el WOD.

El ejemplo de clase es un chat. Este proyecto usa las mismas técnicas (`fetch` cada pocos segundos, JSON, DOM), pero añade más cosas:

- la página la manda el servidor ya rellena y después JavaScript la mantiene al día;
- hay sesiones con dos roles, socio y monitor;
- la validación se hace en el cliente y en el servidor con las mismas reglas;
- la tabla se reordena moviendo las filas que ya existen, sin redibujarla entera;
- cuando el monitor publica otro WOD, la estructura de la página cambia: el campo de la marca pasa de tiempo a repeticiones, se vacían la tabla y los ánimos, y se ilumina la cabecera.

## Cómo se usa

1. Con XAMPP, abrir la carpeta `velnora-pizarra` en el navegador. La base de datos `datos/pizarra.db` se crea sola con un WOD de ejemplo.
2. Entrar con un nombre como **socio**, o como **monitor** con el PIN `velnora`.
3. Abrir otra ventana privada con otro nombre para ver cómo se actualiza la clasificación en directo.

## Archivos

| Archivo | Qué hace |
|---|---|
| `index.php` | Entrada (formulario normal) y pizarra. El servidor pinta el WOD, la clasificación y los ánimos al cargar. |
| `api.php` | API JSON: `estado` (GET), y `resultado`, `borrar_resultado`, `animo` y `wod` (POST con cabecera `X-Token`). Responde con 200, 201, 400, 401, 403, 405 y 422. |
| `js/pizarra.js` | Pide el estado cada 2 segundos y modifica el DOM: reordena y destaca filas, añade ánimos, cambia el WOD y valida los formularios. |
| `includes/bd.php` | Conexión SQLite y creación de las tablas `wods`, `resultados` y `animos`. |
| `includes/valores.php` | Pasa `6:45` a segundos y al revés, y valida las marcas. Usa las mismas reglas que el JavaScript. |
| `includes/funciones.php` | Sesión, token, WOD actual, clasificación (primero los RX y luego los escalados) y ánimos. |
| `salir.php` | Cierra la sesión. |
| `pruebas.php` | 38 pruebas: funciones y API por HTTP con dos usuarios a la vez. |

## Pruebas

```
php pruebas.php
```

Usa una base de datos temporal, así que no toca la de verdad.
