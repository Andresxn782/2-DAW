<?php
	include "VelnoraJSON.php";

	$velnoraJSON = new VelnoraJSON();

	// GUARDAR: la ficha de un socio con datos anidados
	$socio = [
		"nombre" => "Lucía",
		"apellidos" => "Martín Soler",
		"plan" => "Anual",
		"contacto" => [
			"email" => "lucia.martin@velnora.es",
			"telefonos" => ["movil" => "611223344"]
		],
		"reservas" => [
			["clase" => "Spinning", "fecha" => "2026-10-19"],
			["clase" => "Yoga", "fecha" => "2026-10-21"]
		]
	];
	$velnoraJSON->guardarJSON("datos.json", $socio);

	// CARGAR
	$cargado = $velnoraJSON->cargarJSON("datos.json");
	echo "<p>".$cargado['nombre']." tiene ".count($cargado['reservas'])." reservas. Su móvil es ".$cargado['contacto']['telefonos']['movil'].".</p>";

	// AÑADIR a una lista: registro de accesos al gimnasio
	$velnoraJSON->anadir("accesos.json", ["socio" => "Lucía", "hora" => date("Y-m-d H:i:s")]);
	echo "<p>Accesos registrados: ".count($velnoraJSON->cargarJSON("accesos.json"))."</p>";

	echo "<pre>".htmlspecialchars(file_get_contents("datos.json"))."</pre>";
?>
