# Utilización de información proveniente de repositorios

Construyo **Velnora TV**, el catálogo de clases grabadas del gimnasio. La información sale de un repositorio:

- `repositorio/entrenos.json`: 60 vídeos de entrenamiento con título, categoría, nivel, duración, monitor y descripción. Lo crea `repositorio/generar.php` (se puede volver a ejecutar).
- `repositorio/miniatura.php?id=7`: dibuja la carátula de cada vídeo en SVG, así no hace falta guardar imágenes.

| Paso | Qué añade |
|---|---|
| 001 | El servidor devuelve los 12 primeros y el cliente los pinta con una `<template>` |
| 002 | Hoja de estilos tipo plataforma de streaming |
| 003 | Botón "Cargar más" que pide la página siguiente |
| 004 | Scroll infinito con `IntersectionObserver`, filtros por categoría y nivel, búsqueda y ficha del vídeo |
