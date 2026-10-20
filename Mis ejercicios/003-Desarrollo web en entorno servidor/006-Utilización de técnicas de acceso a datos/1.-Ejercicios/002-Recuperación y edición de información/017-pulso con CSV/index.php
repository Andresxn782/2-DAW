<?php
	// Punto de entrada: monta el MVC. Aquí no hay ni SQL ni HTML.
	session_start();
	require_once __DIR__."/config.php";
	require_once __DIR__."/Clases/modelo.php";
	require_once __DIR__."/Clases/controlador.php";
	require_once __DIR__."/Clases/vista.php";

	$modelo = new VelnoraModelo($config['ruta_datos'], $config['basededatos'], $config['claves_primarias']);
	$controlador = new VelnoraControlador($modelo);
	$vista = new VelnoraVista($config);

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
