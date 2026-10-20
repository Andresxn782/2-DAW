# Qué es un servidor web

Un ordenador encendido las 24 horas que **escucha** peticiones y **responde** con información.

Suele tener:
- Una base de datos (MySQL, SQLite...).
- Un servicio web:
  - Apache2 + PHP
  - NGINX + PHP
  - Node.js
  - Gunicorn + Python

Con PHP lo primero es instalar un servidor web.

## Mi instalación (Windows)
Uso **XAMPP**, instalado en `C:\xaampp`. Mis ejercicios están en `C:\xaampp\htdocs\2-DAW`
y los abro en `http://localhost/2-DAW/...`.

## En Linux sería
```
sudo apt install apache2
sudo apt install php
sudo apt install mysql-server
sudo apt install sqlite3
sudo apt install php-sqlite3
```
