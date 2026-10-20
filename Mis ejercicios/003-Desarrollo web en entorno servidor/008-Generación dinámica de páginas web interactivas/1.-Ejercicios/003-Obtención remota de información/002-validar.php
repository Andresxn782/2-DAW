<?php
	// Valida el alta en el servidor. Responde:
	//   200 {"ok": true, "id": 16}                         si todo está bien
	//   422 {"ok": false, "errores": {"campo": "texto"}}   si algo falla
	header("Content-Type: application/json; charset=UTF-8");

	if ($_SERVER['REQUEST_METHOD'] !== "POST") {
		http_response_code(405);
		echo json_encode(["ok" => false, "errores" => ["general" => "Usa POST"]]);
		exit;
	}

	$planes = ["Musculación", "Spinning", "Crossfit", "Yoga", "Pilates", "Natación"];
	$datos = [
		"nombre" => trim($_POST['nombre'] ?? ""),
		"apellidos" => trim($_POST['apellidos'] ?? ""),
		"email" => trim($_POST['email'] ?? ""),
		"plan" => $_POST['plan'] ?? "",
		"nacimiento" => $_POST['nacimiento'] ?? ""
	];
	$errores = [];

	if (!preg_match('/^[\p{L} \'-]{2,30}$/u', $datos['nombre'])) {
		$errores['nombre'] = "Entre 2 y 30 letras";
	}
	if (!preg_match('/^[\p{L} \'-]{2,50}$/u', $datos['apellidos'])) {
		$errores['apellidos'] = "Entre 2 y 50 letras";
	}

	$db = new PDO("sqlite:".__DIR__."/socios.db");
	if (filter_var($datos['email'], FILTER_VALIDATE_EMAIL) === false) {
		$errores['email'] = "No es un email válido";
	} else {
		$consulta = $db->prepare("SELECT COUNT(*) FROM socios WHERE lower(email) = lower(?)");
		$consulta->execute([$datos['email']]);
		if ($consulta->fetchColumn() > 0) {
			$errores['email'] = "Ya hay un socio con ese email";
		}
	}

	if (!in_array($datos['plan'], $planes, true)) {
		$errores['plan'] = "Elige un plan de la lista";
	}

	// Edad mínima 16 años
	$fecha = DateTime::createFromFormat("!Y-m-d", $datos['nacimiento']);
	if ($fecha === false || $fecha->format("Y-m-d") !== $datos['nacimiento']) {
		$errores['nacimiento'] = "Fecha no válida";
	} else if ($fecha->diff(new DateTime("today"))->y < 16 || $fecha > new DateTime("today")) {
		$errores['nacimiento'] = "Hay que tener al menos 16 años";
	}

	if (count($errores) > 0) {
		http_response_code(422);
		echo json_encode(["ok" => false, "errores" => $errores], JSON_UNESCAPED_UNICODE);
		exit;
	}

	// La tabla socios no tiene fecha de nacimiento: guardo el resto
	$consulta = $db->prepare("INSERT INTO socios (nombre, apellidos, email, plan) VALUES (?, ?, ?, ?)");
	$consulta->execute([$datos['nombre'], $datos['apellidos'], $datos['email'], $datos['plan']]);
	echo json_encode(["ok" => true, "id" => (int)$db->lastInsertId()]);
?>
