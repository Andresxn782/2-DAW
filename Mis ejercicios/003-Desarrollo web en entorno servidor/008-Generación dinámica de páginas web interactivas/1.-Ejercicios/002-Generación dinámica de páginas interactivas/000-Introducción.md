# Generación dinámica de páginas interactivas

Construyo paso a paso **Velnora Sala**, un chat para que los socios que están entrenando a la vez se animen y avisen ("¿alguien me ayuda con la barra?").

| Paso | Qué añade |
|---|---|
| 001 | Pantalla de entrada (solo HTML y CSS) |
| 002 | Guardar el nombre en `localStorage` y ocultar la entrada |
| 003 | Mostrar la caja del chat |
| 004 | Estilo del chat (en `chat.css`, que usan todos los pasos siguientes) |
| 005 | `servidor.php` devuelve los mensajes en JSON |
| 006 | El cliente pregunta al servidor cada segundo |
| 007 | Los mensajes se pintan en la página |
| 008 | Enviar mensajes, pedir solo los nuevos y validar en el servidor |

La base de datos `chat.db` de cada carpeta la crea `servidor.php` la primera vez.
