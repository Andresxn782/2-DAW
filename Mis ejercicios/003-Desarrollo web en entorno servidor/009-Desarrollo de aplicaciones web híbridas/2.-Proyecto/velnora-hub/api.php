<?php
	// Una fuente cada vez:  api.php?fuente=clases|tiempo|rutas|afluencia|videos
	// Así, si una falla (por ejemplo la AEMET), las demás se siguen viendo.
	require __DIR__."/inicio.php";
	header("Content-Type: application/json; charset=UTF-8");

	$nombre = $_GET['fuente'] ?? "";
	$validas = ["clases", "tiempo", "rutas", "afluencia", "videos"];
	if (!in_array($nombre, $validas, true)) {
		http_response_code(404);
		echo json_encode(["error" => "Fuente desconocida. Usa: ".implode(", ", $validas)], JSON_UNESCAPED_UNICODE);
		exit;
	}

	try {
		$resultado = consultar($nombre, $config, $cache);
		echo json_encode(["fuente" => $nombre] + $resultado, JSON_UNESCAPED_UNICODE);
	} catch (Throwable $error) {
		error_log("Velnora Hub (".$nombre."): ".$error->getMessage());
		http_response_code(502);
		echo json_encode(["error" => "No se ha podido leer la fuente ".$nombre], JSON_UNESCAPED_UNICODE);
	}
?>
