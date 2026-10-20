<?php
	session_start();
	require_once __DIR__."/config.php";
	require_once __DIR__."/Clases/modelo.php";
	require_once __DIR__."/Clases/controlador.php";

	$controlador = new VelnoraControlador(new VelnoraModelo($config['ruta_datos'], $config['basededatos'], $config['claves_primarias']));
	$controlador->logout();
	header("Location: index.php");
	exit();
?>
