# Herramientas para probar un servicio web

| Herramienta | Cómo la uso |
|---|---|
| Navegador | Solo sirve para peticiones `GET` sin cabeceras. Con F12, pestaña Red, veo el código y las cabeceras. |
| `curl` | `curl -i -H "X-API-Key: velnora_2026_clave_segura" "http://localhost/.../api.php?id=3"`. Con `-X POST -d '{...}'` mando datos. |
| Postman / Thunder Client | Programas con ventanas: guardo colecciones de peticiones, cabeceras y variables. |
| Script propio | `003-probar api.php` comprueba códigos y contenidos de forma automática; si algo falla, termina con código 1. |

Qué compruebo siempre:

1. El código de estado (200, 201, 401, 404…).
2. La cabecera `Content-Type`.
3. Que el cuerpo sea un JSON válido con los campos del contrato.
4. Los casos malos: sin clave, con datos que faltan, con un id que no existe.
