<?php
session_start();
require_once 'modelo.php';
require_once 'controlador.php';
$modelo = new VelnoraModelo(__DIR__.'/empresa.db');
$controlador = new VelnoraControlador($modelo);
$controlador->logout();
header('Location:index.php');
exit();
