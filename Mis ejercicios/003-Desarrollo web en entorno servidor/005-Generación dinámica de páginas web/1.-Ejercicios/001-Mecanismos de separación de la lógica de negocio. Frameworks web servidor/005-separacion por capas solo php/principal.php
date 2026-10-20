<?php
	include "controlador.php";
	include "vista.php";

	// La misma vista pinta dos tablas distintas
	$datos = dameDatos("socios");
	pintaTabla($datos, "Socios");

	$datos = dameDatos("material");
	pintaTabla($datos, "Material del gimnasio");
?>
