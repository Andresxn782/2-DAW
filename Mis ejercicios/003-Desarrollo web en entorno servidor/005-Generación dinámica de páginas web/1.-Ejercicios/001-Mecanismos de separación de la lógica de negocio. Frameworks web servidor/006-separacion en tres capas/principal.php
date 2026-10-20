<?php
	include "modelo.php";
	include "vista.php";
	include "controlador.php";

	/* Paso a paso sería:
	$datos = dameDatos("material");
	$json = coseJson($datos);
	pintaTabla($json);
	*/

	// Y todo en una línea:
	pintaTabla(coseJson(dameDatos("material")), "Material del gimnasio");
?>
