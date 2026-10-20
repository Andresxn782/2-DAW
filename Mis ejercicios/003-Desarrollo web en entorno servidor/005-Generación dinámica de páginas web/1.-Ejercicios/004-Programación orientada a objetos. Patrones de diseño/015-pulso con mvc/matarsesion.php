<?php
	session_start();
	require_once "modelo.php";
	require_once "controlador.php";

	$controlador = new VelnoraControlador(new VelnoraModelo(__DIR__."/gimnasio.db"));
	$controlador->logout();
	header("Location: index.php");
	exit();
?>
