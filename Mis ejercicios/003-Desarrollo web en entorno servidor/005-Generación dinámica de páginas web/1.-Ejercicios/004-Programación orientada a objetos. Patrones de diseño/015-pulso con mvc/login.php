<?php
	session_start();
	require_once "modelo.php";
	require_once "controlador.php";

	if($_SERVER['REQUEST_METHOD'] !== 'POST'){
		header("Location: index.php");
		exit();
	}
	$controlador = new VelnoraControlador(new VelnoraModelo(__DIR__."/gimnasio.db"));
	$ok = $controlador->login($_POST['usuario'] ?? '', $_POST['contrasena'] ?? '');
	header("Location: index.php".($ok ? "" : "?error=1"));
	exit();
?>
