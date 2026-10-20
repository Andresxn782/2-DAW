<?php
	// Software A: servicio web de socios de Velnora protegido con API Key.
	// La clave viaja en una cabecera HTTP, no en la URL, para que no
	// quede guardada en el historial ni en los registros del servidor.
	$API_KEY_CORRECTA = "velnora_2026_clave_segura";

	header("Content-Type: application/json; charset=UTF-8");

	// IMPORTANTE: PHP convierte la cabecera X-API-Key en HTTP_X_API_KEY
	$api_key = $_SERVER["HTTP_X_API_KEY"] ?? "";

	// hash_equals compara sin dar pistas por el tiempo que tarda
	if (!hash_equals($API_KEY_CORRECTA, $api_key)) {
		http_response_code(401);
		echo json_encode([
			"error" => true,
			"mensaje" => "API Key incorrecta o no proporcionada"
		], JSON_UNESCAPED_UNICODE);
		exit;
	}

	$baseDatos = new SQLite3(__DIR__."/socios.db");

	// Si me piden un socio concreto (?id=3) devuelvo solo ese
	if (isset($_GET['id'])) {
		$consulta = $baseDatos->prepare("SELECT id, nombre, apellidos, email, plan FROM socios WHERE id = :id");
		$consulta->bindValue(":id", (int)$_GET['id'], SQLITE3_INTEGER);
		$socio = $consulta->execute()->fetchArray(SQLITE3_ASSOC);
		if ($socio === false) {
			http_response_code(404);
			echo json_encode(["error" => true, "mensaje" => "No existe ese socio"], JSON_UNESCAPED_UNICODE);
			exit;
		}
		echo json_encode($socio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		exit;
	}

	// Si no, la lista completa (opcionalmente filtrada por plan)
	if (isset($_GET['plan'])) {
		$consulta = $baseDatos->prepare("SELECT id, nombre, apellidos, email, plan FROM socios WHERE plan = :plan ORDER BY id");
		$consulta->bindValue(":plan", $_GET['plan'], SQLITE3_TEXT);
		$resultado = $consulta->execute();
	} else {
		$resultado = $baseDatos->query("SELECT id, nombre, apellidos, email, plan FROM socios ORDER BY id");
	}

	$socios = [];
	while ($fila = $resultado->fetchArray(SQLITE3_ASSOC)) {
		$socios[] = $fila;
	}

	http_response_code(200);
	echo json_encode([
		"total" => count($socios),
		"socios" => $socios
	], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
