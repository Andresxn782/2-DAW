<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Clases/modelo.php';
require_once __DIR__ . '/Clases/controlador.php';
$modelo = new VelnoraModelo($config['ruta_datos'], $config['basededatos'], $config['claves_primarias']);
$controlador = new VelnoraControlador($modelo);
$controlador->logout();
header('Location:index.php');
exit();