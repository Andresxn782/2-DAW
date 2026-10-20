<?php
	// Datos para las gráficas de actividad, en el formato que espera Chart.js
	require __DIR__."/conexion.php";
	$db = abrirComunidad();
	header("Content-Type: application/json; charset=UTF-8");

	// Publicaciones por día en los últimos 30 días (los días sin publicaciones valen 0)
	$porDia = $db->query("SELECT date(fecha) AS dia, COUNT(*) FROM publicaciones
		WHERE fecha >= date('now', 'localtime', '-29 days') GROUP BY dia")->fetchAll(PDO::FETCH_KEY_PAIR);
	$dias = [];
	$cantidades = [];
	for ($i = 29; $i >= 0; $i--) {
		$dia = date("Y-m-d", strtotime("-".$i." days"));
		$dias[] = date("d/m", strtotime($dia));
		$cantidades[] = (int)($porDia[$dia] ?? 0);
	}

	$porTipo = $db->query("SELECT tipo, COUNT(*) FROM reacciones GROUP BY tipo ORDER BY tipo")->fetchAll(PDO::FETCH_KEY_PAIR);
	$populares = $db->query("SELECT s.nombre || ' ' || substr(s.apellidos, 1, 1) || '.' AS socio, COUNT(*) AS seguidores
		FROM seguimientos g JOIN socios s ON s.id = g.id_seguido
		GROUP BY g.id_seguido ORDER BY seguidores DESC, s.id LIMIT 5")->fetchAll(PDO::FETCH_KEY_PAIR);

	echo json_encode([
		"publicaciones" => ["etiquetas" => $dias, "valores" => $cantidades],
		"reacciones" => ["etiquetas" => array_keys($porTipo), "valores" => array_map("intval", array_values($porTipo))],
		"populares" => ["etiquetas" => array_keys($populares), "valores" => array_map("intval", array_values($populares))]
	], JSON_UNESCAPED_UNICODE);
?>
