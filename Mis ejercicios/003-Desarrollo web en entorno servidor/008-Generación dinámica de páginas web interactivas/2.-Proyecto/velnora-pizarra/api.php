<?php
	// API de la pizarra. La usa js/pizarra.js con fetch.
	//   GET  api.php?accion=estado&wod=1&animos_desde=5
	//   POST api.php?accion=resultado | borrar_resultado | animo | wod
	// Todas las respuestas son JSON. Los POST llevan la cabecera X-Token.
	require __DIR__."/includes/funciones.php";
	header("Content-Type: application/json; charset=UTF-8");

	function responder($codigo, $datos) {
		http_response_code($codigo);
		echo json_encode($datos, JSON_UNESCAPED_UNICODE);
		exit;
	}

	if (usuario() === null) {
		responder(401, ["ok" => false, "error" => "Tienes que entrar en la pizarra"]);
	}

	$accion = $_GET['accion'] ?? "";
	$db = conectar();
	$wod = wodActual($db);

	if ($_SERVER['REQUEST_METHOD'] === "POST") {
		if (!hash_equals(token(), $_SERVER['HTTP_X_TOKEN'] ?? "")) {
			responder(403, ["ok" => false, "error" => "Token no válido. Recarga la página."]);
		}
	} else if ($accion !== "estado") {
		responder(405, ["ok" => false, "error" => "Esta acción necesita POST"]);
	}

	$socio = usuario()['nombre'];

	switch ($accion) {
		case "estado":
			// Si el cliente tiene otro WOD, le mando también los datos del nuevo
			// y todos los ánimos desde el principio
			$wodCliente = (int)($_GET['wod'] ?? 0);
			$desde = $wodCliente === (int)$wod['id'] ? (int)($_GET['animos_desde'] ?? 0) : 0;
			responder(200, [
				"ok" => true,
				"wod" => [
					"id" => (int)$wod['id'],
					"titulo" => $wod['titulo'],
					"descripcion" => $wod['descripcion'],
					"tipo" => $wod['tipo'],
					"monitor" => $wod['monitor']
				],
				"wod_nuevo" => $wodCliente !== (int)$wod['id'],
				"ranking" => ranking($db, $wod),
				"animos" => animos($db, $wod, $desde)
			]);

		case "resultado":
			$valor = interpretarValor($_POST['valor'] ?? "", $wod['tipo']);
			if (is_string($valor)) {
				responder(422, ["ok" => false, "error" => $valor, "campo" => "valor"]);
			}
			$escalado = !empty($_POST['escalado']) ? 1 : 0;
			// Si ya tenía resultado lo sustituyo (un socio, un resultado por WOD)
			$consulta = $db->prepare("INSERT INTO resultados (wod_id, socio, valor, escalado) VALUES (?, ?, ?, ?)
				ON CONFLICT (wod_id, socio) DO UPDATE SET valor = excluded.valor, escalado = excluded.escalado,
				actualizado = datetime('now', 'localtime')");
			$consulta->execute([$wod['id'], $socio, $valor, $escalado]);
			$ranking = ranking($db, $wod);
			$posicion = 0;
			foreach ($ranking as $fila) {
				if ($fila['socio'] === $socio) {
					$posicion = $fila['posicion'];
				}
			}
			responder(200, ["ok" => true, "posicion" => $posicion, "texto" => formatearValor($valor, $wod['tipo'])]);

		case "borrar_resultado":
			$consulta = $db->prepare("DELETE FROM resultados WHERE wod_id = ? AND socio = ?");
			$consulta->execute([$wod['id'], $socio]);
			responder(200, ["ok" => true, "borrado" => $consulta->rowCount() > 0]);

		case "animo":
			$texto = trim($_POST['texto'] ?? "");
			if ($texto === "" || mb_strlen($texto) > 140) {
				responder(422, ["ok" => false, "error" => "El mensaje debe tener entre 1 y 140 caracteres", "campo" => "texto"]);
			}
			$consulta = $db->prepare("INSERT INTO animos (wod_id, socio, texto) VALUES (?, ?, ?)");
			$consulta->execute([$wod['id'], $socio, $texto]);
			responder(201, ["ok" => true, "id" => (int)$db->lastInsertId()]);

		case "wod":
			if (!esMonitor()) {
				responder(403, ["ok" => false, "error" => "Solo los monitores pueden publicar el WOD"]);
			}
			$titulo = trim($_POST['titulo'] ?? "");
			$descripcion = trim($_POST['descripcion'] ?? "");
			$tipo = $_POST['tipo'] ?? "";
			$errores = [];
			if ($titulo === "" || mb_strlen($titulo) > 60) {
				$errores['titulo'] = "Entre 1 y 60 caracteres";
			}
			if ($descripcion === "" || mb_strlen($descripcion) > 300) {
				$errores['descripcion'] = "Entre 1 y 300 caracteres";
			}
			if (!in_array($tipo, ["tiempo", "repeticiones"], true)) {
				$errores['tipo'] = "Elige tiempo o repeticiones";
			}
			if (count($errores) > 0) {
				responder(422, ["ok" => false, "error" => "Revisa los datos del WOD", "errores" => $errores]);
			}
			$consulta = $db->prepare("INSERT INTO wods (titulo, descripcion, tipo, monitor) VALUES (?, ?, ?, ?)");
			$consulta->execute([$titulo, $descripcion, $tipo, $socio]);
			responder(201, ["ok" => true, "id" => (int)$db->lastInsertId()]);

		default:
			responder(400, ["ok" => false, "error" => "Acción no válida"]);
	}
?>
