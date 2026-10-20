# Acceso a datos desde PHP

Una aplicación web guarda la información en un **almacén de datos**: una base de datos relacional (SQLite, MySQL/MariaDB, PostgreSQL), archivos (CSV, JSON, XML, imágenes) o servicios externos (APIs).

## Formas de conectar con una base de datos

| Extensión | Bases de datos | Cómo se conecta |
|---|---|---|
| `SQLite3` | Solo SQLite | `new SQLite3('gimnasio.db')` |
| `mysqli` | Solo MySQL/MariaDB | `new mysqli('localhost', 'root', '', 'velnora')` |
| `PDO` | Casi todas (SQLite, MySQL, PostgreSQL...) | `new PDO('mysql:host=localhost;dbname=velnora;charset=utf8mb4', 'root', '')` |

Uso **PDO** siempre que puedo, porque:

- si cambio de SQLite a MySQL solo cambia la cadena de conexión (el DSN);
- tiene consultas preparadas con marcadores (`:id`), que evitan la inyección SQL;
- puede lanzar excepciones (`PDO::ERRMODE_EXCEPTION`), que se capturan con `try/catch`;
- devuelve los resultados como arrays, objetos o instancias de mis clases.

## Pasos de cualquier acceso

1. Conectar (y capturar el error si falla).
2. Preparar la consulta.
3. Ejecutarla con los valores.
4. Recorrer el conjunto de resultados.
5. Cerrar la conexión (en PDO, basta con que la variable deje de existir o asignarle `null`).

## Seguridad

- Nunca meter en la consulta valores que vengan del usuario sin preparar.
- No poner la contraseña de la base de datos en archivos públicos ni subirla a GitHub en un proyecto real.
- Con SQLite, el archivo `.db` no debería estar dentro de la carpeta pública del servidor.
