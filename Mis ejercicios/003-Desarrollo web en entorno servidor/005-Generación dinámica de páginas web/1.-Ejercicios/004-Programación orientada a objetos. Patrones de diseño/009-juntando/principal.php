<?php
	include "vista.php";
	include "modelo.php";
	include "controlador.php";

	// Con métodos estáticos no hace falta crear el objeto modelo
	echo "<p>".VelnoraModelo::cuenta("socios.db", "socios")." socios y ".VelnoraModelo::cuenta("socios.db", "material")." tipos de material</p>";

	$material = VelnoraModelo::dameDatosEstatico("socios.db", "material");
	$vista = new VelnoraVista(coseJson($material));
	$vista->pintaTarjetas("nombre");
?>
