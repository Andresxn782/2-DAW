<?php
	// Software A: ahora el JSON lo genera json_encode a partir de un array.
	// Así nunca se me olvida una comilla ni una coma.
	header("Content-Type: application/json; charset=UTF-8");

	$monitor = [
		"nombre" => "Andres",
		"apellidos" => "Ruiz",
		"email" => "andres.ruiz@velnora.es",
		"clases" => ["Spinning", "Crossfit", "Pilates"],
		"activo" => true
	];

	echo json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
