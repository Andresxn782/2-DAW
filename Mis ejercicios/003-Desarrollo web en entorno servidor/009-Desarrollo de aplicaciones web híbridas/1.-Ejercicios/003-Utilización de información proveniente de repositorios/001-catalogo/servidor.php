<?php
	// Leo el repositorio JSON y devuelvo los 12 primeros vídeos
	$json = file_get_contents(__DIR__."/../repositorio/entrenos.json");
	$datos = json_decode($json, true);
	$entrenos = array_slice($datos["entrenos"], 0, 12);

	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($entrenos, JSON_UNESCAPED_UNICODE);
?>
