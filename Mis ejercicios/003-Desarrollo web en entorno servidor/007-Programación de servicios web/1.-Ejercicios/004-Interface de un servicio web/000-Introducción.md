# Interface de un servicio web

Consumo un servicio web real: la predicción por municipios de la AEMET, que se publica en XML. La uso para decidir si las clases de running al aire libre de Velnora se hacen fuera o dentro.

- Municipio: Madrid, código `28079` (`https://www.aemet.es/xml/municipios/localidad_28079.xml`). Se cambia en `aemet.php`.
- Si no hay internet, los ejercicios del 004 al 006 usan `prediccion-ejemplo.xml`, una predicción de ejemplo con el mismo formato que publica la AEMET.
