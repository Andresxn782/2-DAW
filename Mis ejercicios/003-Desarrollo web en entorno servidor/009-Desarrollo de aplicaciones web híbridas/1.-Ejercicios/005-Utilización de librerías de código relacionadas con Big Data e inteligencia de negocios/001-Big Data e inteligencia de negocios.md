# Big Data e inteligencia de negocios

## Big Data

Se habla de Big Data cuando los datos son tantos, llegan tan rápido o son tan variados que las herramientas normales no bastan. Se suele resumir en las "V":

- **Volumen**: millones de registros (todos los accesos de todos los gimnasios de una cadena).
- **Velocidad**: datos que no paran de llegar (el torno, los relojes de los socios).
- **Variedad**: tablas, JSON, registros de texto, imágenes...
- **Veracidad**: datos con errores o incompletos que hay que limpiar.
- **Valor**: solo merece la pena si sirve para decidir algo.

## Inteligencia de negocios (BI)

Es convertir datos en decisiones. El proceso típico es **ETL**:

1. **Extraer** los datos de donde estén (el CSV del torno, la base de datos de socios).
2. **Transformar**: limpiar, agrupar y calcular indicadores (KPI).
3. **Cargar** el resultado donde se va a consultar: un cuadro de mando.

Indicadores que me interesan en Velnora:

- accesos por hora y día de la semana, para saber cuándo reforzar monitores;
- accesos por mes, para ver la estacionalidad (enero sube, agosto baja);
- socios activos por mes y duración media de la visita;
- uso por plan.

## Herramientas y librerías

| Herramienta | Para qué |
|---|---|
| Hadoop, Spark | Procesar datos repartidos entre muchas máquinas |
| pandas, Polars (Python) | Analizar tablas grandes en un ordenador |
| Bases de datos analíticas: ClickHouse, BigQuery, DuckDB | Consultas SQL muy rápidas sobre millones de filas |
| Power BI, Tableau, Metabase, Grafana | Cuadros de mando sin programar |
| Chart.js, D3, ECharts | Gráficas en la web |
| En PHP: generadores (`yield`), `SplFileObject`, `league/csv` | Leer archivos enormes sin cargarlos enteros en memoria |

## Lo que hago yo

Mi volumen (unos 230.000 registros) cabe en un ordenador, pero aplico las mismas ideas que con datos más grandes:

- leo el CSV **en streaming** con un generador, sin cargarlo entero, para que la memoria no dependa del tamaño del archivo (`003-memoria.php` lo demuestra);
- calculo todos los indicadores en **una sola pasada**;
- guardo el resultado en una **caché**, que se vuelve a calcular solo si el CSV cambia;
- el cuadro de mando solo recibe los agregados (unos pocos KB), nunca los datos en bruto;
- las gráficas las dibuja **Chart.js**, una librería de terceros.
