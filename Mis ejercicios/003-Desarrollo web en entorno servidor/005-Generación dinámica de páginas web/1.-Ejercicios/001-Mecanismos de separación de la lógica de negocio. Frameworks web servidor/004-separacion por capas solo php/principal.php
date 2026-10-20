<?php
	// El archivo principal solo une las dos partes
	include "controlador.php";
	include "vista.php";

	$datos = dameDatos();
	pintaTabla($datos, "Socios");
?>
