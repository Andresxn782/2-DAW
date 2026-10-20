<?php
	// Ahora el cliente pide una página concreta: ?pagina=0, 1, 2...
	$porPagina = 12;
	$pagina = max(0, (int)($_GET['pagina'] ?? 0));

	$datos = json_decode(file_get_contents(__DIR__."/../repositorio/entrenos.json"), true);
	$entrenos = array_slice($datos["entrenos"], $pagina * $porPagina, $porPagina);

	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode([
		"entrenos" => $entrenos,
		// Así el cliente sabe si tiene que seguir enseñando el botón
		"hayMas" => ($pagina + 1) * $porPagina < count($datos["entrenos"])
	], JSON_UNESCAPED_UNICODE);
?>
