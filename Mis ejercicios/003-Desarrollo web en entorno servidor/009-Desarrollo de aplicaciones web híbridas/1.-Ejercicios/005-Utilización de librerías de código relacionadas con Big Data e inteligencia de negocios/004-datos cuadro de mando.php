<?php
	// Devuelve los indicadores ya calculados (desde la caché si se puede)
	require __DIR__."/AnalizadorAccesos.php";
	header("Content-Type: application/json; charset=UTF-8");

	$archivo = __DIR__."/accesos.csv";
	if (!file_exists($archivo)) {
		http_response_code(503);
		echo json_encode(["error" => "Faltan los datos. Ejecuta en la consola: php \"002-generar accesos.php\""], JSON_UNESCAPED_UNICODE);
		exit;
	}

	echo json_encode((new AnalizadorAccesos($archivo))->indicadores(), JSON_UNESCAPED_UNICODE);
?>
