# Llamar a la API desde la terminal

Con XAMPP arrancado, `curl` hace la petición igual que lo haría otro programa. Los espacios y los acentos de la ruta van codificados (`%20`, `%C3%A1`…):

```
curl "http://localhost/2-DAW/Mis%20ejercicios/003-Desarrollo%20web%20en%20entorno%20servidor/007-Programaci%C3%B3n%20de%20servicios%20web/1.-Ejercicios/002-Est%C3%A1ndares%20y%20arquitecturas%20actuales.%20Formatos%20de%20intercambio%20de%20datos/001-API/SoftwareA/api.php"
```

Con `-i` se ven también las cabeceras y el código de estado:

```
curl -i "http://localhost/.../001-API/SoftwareA/api.php"
```

Para no escribir la ruta tan larga, también puedo arrancar el servidor de PHP dentro de la carpeta:

```
cd "001-API/SoftwareA"
php -S localhost:8000
curl -i http://localhost:8000/api.php
```
