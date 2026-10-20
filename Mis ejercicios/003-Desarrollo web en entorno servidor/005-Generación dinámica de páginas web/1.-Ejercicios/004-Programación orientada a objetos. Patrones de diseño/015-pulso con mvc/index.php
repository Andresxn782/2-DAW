<?php
	// Punto de entrada: monta el MVC. Aquí no hay ni SQL ni HTML.
	session_start();
	require_once "modelo.php";
	require_once "controlador.php";
	require_once "vista.php";

	$modelo = new VelnoraModelo(__DIR__."/gimnasio.db");
	$controlador = new VelnoraControlador($modelo);
	$vista = new VelnoraVista();

	if(!$controlador->estaConectado()){
		$vista->principio();
		$vista->login(isset($_GET['error']));
	}else{
		$estado = $controlador->procesaCrud($controlador->tablaSolicitada());
		$vista->principio();
		$vista->abrePanel($_SESSION['nombre'], $controlador->tablasPermitidas(), $estado);
		if($estado['accion'] === 'nuevo' || $estado['accion'] === 'editar'){
			$vista->pintaFormulario($estado);
		}else{
			$vista->pintaTabla($estado);
		}
		$vista->cierraPanel();
	}
	$vista->final();
?>
