<?php
	// Expongo solo la parte visual (nunca la ruta de la base de datos)
	require __DIR__."/config.php";
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([
		'nombre' => $config['nombre'],
		'subtitulo' => $config['subtitulo'],
		'logo' => $config['logo'],
		'color' => $config['color']
	], JSON_UNESCAPED_UNICODE);
?>
