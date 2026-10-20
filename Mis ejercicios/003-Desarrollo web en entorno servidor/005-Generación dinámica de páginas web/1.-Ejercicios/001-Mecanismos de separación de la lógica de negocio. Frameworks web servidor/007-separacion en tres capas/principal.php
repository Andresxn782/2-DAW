<?php
	include "modelo.php";
	include "vista.php";
	include "controlador.php";

	$datos = dameDatos("material");
	$json = coseJson($datos);

	$vista = new VelnoraVista($json);
	echo "<h2>Nuevo material</h2>";
	$vista->pintaFormulario();
	echo "<h2>Material en tarjetas</h2>";
	$vista->pintaTarjetas("nombre");
?>
