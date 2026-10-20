<?php
	// Información que el servidor ofrece para que otros la reutilicen
	header("Content-Type: application/json; charset=UTF-8");
	$monitor = [
		"nombre" => "Andres",
		"apellidos" => "Ruiz",
		"email" => "andres.ruiz@velnora.es",
		"nacimiento" => "2001-05-10",
		"alta" => "2023-09-01",
		"clases_semana" => 9
	];

	echo json_encode($monitor, JSON_UNESCAPED_UNICODE);
?>
