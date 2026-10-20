# Modificación de la estructura y contenido de la página web

- `001-rutina.html`: monto una rutina de entrenamiento cambiando el DOM: añado filas, las borro, las muevo, las marco como hechas y recalculo los totales. Se guarda en `localStorage`.
- `002-horario.php` + `clases.php`: dos formas de cambiar un trozo de la página con datos del servidor:
  - el servidor devuelve **HTML ya hecho** (`?formato=html`) y el cliente lo coloca con `innerHTML`;
  - el servidor devuelve **JSON** (`?formato=json`) y el cliente construye los elementos con una `<template>`.
