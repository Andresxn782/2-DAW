# Velnora API

Proyecto de la unidad 7 (Programación de servicios web). Andres Ruiz.

Es un servicio web REST para las clases del gimnasio Velnora y dos programas que lo usan:

- **`api/`**: la API. Devuelve JSON (o XML si se pide), usa los verbos de HTTP y los códigos de estado, y se protege con una clave en la cabecera `X-API-Key`.
- **`cliente/`**: el panel de recepción. Es otro programa: no abre la base de datos, todo se lo pide a la API con cURL. Abajo enseña la última petición y la respuesta en bruto.
- **`docs/`**: la documentación, generada desde `docs/openapi.json` (OpenAPI 3), con un probador que lanza peticiones reales desde el navegador con `fetch`.

La diferencia con el ejemplo de clase es que aquí la API tiene varios recursos (clases, reservas y socios), todas las operaciones CRUD, dos niveles de permiso, reglas de negocio (aforo, reservas repetidas) y un cliente y una documentación propios.

## Cómo se arranca

Con XAMPP, abrir `http://localhost/2-DAW/Mis ejercicios/003-Desarrollo web en entorno servidor/007-Programación de servicios web/2.-Proyecto/velnora-api/`.

La base de datos `datos/velnora-api.db` se crea sola la primera vez, con 6 clases, 6 socios y algunas reservas.

## Claves

| Clave | Quién | Puede |
|---|---|---|
| `velnora_app_2026` | App de socios | Consultar clases y socios, reservar y cancelar |
| `velnora_admin_2026` | Recepción | Todo lo anterior y crear, modificar y borrar clases |

## Rutas

La ruta va detrás de `api/index.php`, como `api/index.php/clases/3`, o en el parámetro `ruta`, como `api/index.php?ruta=/clases/3`.

| Método | Ruta | Qué hace | Códigos |
|---|---|---|---|
| GET | `/estado` | Comprueba que funciona (sin clave) | 200 |
| GET | `/clases?dia=&monitor=` | Lista las clases con plazas libres | 200, 401 |
| POST | `/clases` | Crea una clase (admin) | 201, 403, 422 |
| GET | `/clases/{id}` | Una clase | 200, 404 |
| PUT | `/clases/{id}` | Modifica los campos enviados (admin) | 200, 400, 409, 422 |
| DELETE | `/clases/{id}` | Borra la clase y sus reservas (admin) | 200, 404 |
| GET | `/clases/{id}/reservas` | Reservas de la clase | 200, 404 |
| POST | `/clases/{id}/reservas` | Reserva `{"socio_id": 3}` | 201, 404, 409, 422 |
| DELETE | `/reservas/{id}` | Cancela una reserva | 200, 404 |
| GET | `/socios` | Lista de socios | 200 |

Ejemplos con curl:

```
curl -i -H "X-API-Key: velnora_app_2026" "http://localhost/.../velnora-api/api/index.php/clases?dia=Lunes"
curl -i -X POST -H "X-API-Key: velnora_app_2026" -H "Content-Type: application/json" -d "{\"socio_id\": 3}" "http://localhost/.../velnora-api/api/index.php/clases/4/reservas"
curl -i -H "X-API-Key: velnora_app_2026" "http://localhost/.../velnora-api/api/index.php/clases/1?formato=xml"
```

## Pruebas

```
php pruebas.php
```

Arranca su propio servidor de PHP con una base de datos temporal y hace 41 comprobaciones con peticiones HTTP reales: autenticación, permisos, filtros, XML, validación, aforo completo, reservas repetidas, borrado en cascada, documentación y panel.

## Archivos

| Archivo | Qué hace |
|---|---|
| `config.php` | Ruta de la base de datos, claves y días de la semana |
| `Clases/BaseDatos.php` | Conexión PDO a SQLite; crea las tablas y los datos de ejemplo |
| `Clases/Respuesta.php` | Envía JSON o XML con el código de estado |
| `Clases/ServicioClases.php` | La lógica: validación, aforo, transacciones; lanza `ErrorApi` con el código adecuado |
| `api/index.php` | Controlador frontal: CORS, clave, lectura del cuerpo y tabla de rutas |
| `cliente/ClienteApi.php` | Clase que hace las peticiones con cURL |
| `cliente/index.php` | Panel de recepción |
| `docs/openapi.json`, `docs/index.php` | Documentación y probador |
| `pruebas.php` | Pruebas automáticas |
