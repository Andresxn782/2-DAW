<?php
	include "vista.php";
	include "modelo.php";
	include "controlador.php";

	// Modelo -> controlador -> vista
	$basededatos = new VelnoraModelo("socios.db");
	$socios = $basededatos->dameDatos("socios");
	$json = coseJson($socios);
	$vista = new VelnoraVista($json);
	$vista->pintaTabla();
?>
