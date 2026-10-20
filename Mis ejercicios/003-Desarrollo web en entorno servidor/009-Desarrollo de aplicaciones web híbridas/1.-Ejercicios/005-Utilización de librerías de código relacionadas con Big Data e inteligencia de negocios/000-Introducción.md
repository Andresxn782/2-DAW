# Utilización de librerías de código relacionadas con Big Data e inteligencia de negocios

Analizo **todos los accesos del torno de Velnora en 2026**: unos 230.000 registros. Con ellos saco los indicadores que necesita la dirección del gimnasio: horas punta, meses fuertes y flojos, socios activos y qué planes se usan más.

| Archivo | Qué hace |
|---|---|
| `001-Big Data e inteligencia de negocios.md` | Conceptos y herramientas |
| `002-generar accesos.php` | Genera `accesos.csv` (unos 8 MB). Se ejecuta una vez: `php "002-generar accesos.php"` |
| `AnalizadorAccesos.php` | Lee el CSV fila a fila con un generador y calcula todo en una sola pasada; guarda el resultado en caché |
| `003-memoria.php` | Compara la memoria que gasta leer el archivo entero contra leerlo con un generador |
| `004-datos cuadro de mando.php` + `004-cuadro de mando.html` | Cuadro de mando con indicadores, mapa de calor y gráficas de Chart.js |

`accesos.csv` no se sube a GitHub porque es grande y se puede volver a generar igual (semilla fija).
