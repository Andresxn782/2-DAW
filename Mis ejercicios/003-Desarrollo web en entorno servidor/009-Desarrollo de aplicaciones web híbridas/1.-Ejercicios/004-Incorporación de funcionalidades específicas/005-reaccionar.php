<?php
	// Pone o quita una reacción del socio de la sesión. Responde
	// {"ok": true, "puesta": bool, "total": número de reacciones de ese tipo}
	session_start();
	require __DIR__."/conexion.php";
	header("Content-Type: application/json; charset=UTF-8");

	if ($_SERVER['REQUEST_METHOD'] !== "POST" || !hash_equals($_SESSION['token'] ?? "x", $_SERVER['HTTP_X_TOKEN'] ?? "")) {
		http_response_code(403);
		echo json_encode(["ok" => false, "error" => "Petición no permitida"]);
		exit;
	}

	$yo = $_SESSION['socio'] ?? 1;
	$publicacion = (int)($_POST['publicacion'] ?? 0);
	$tipo = $_POST['tipo'] ?? "";
	if (!in_array($tipo, ["fuerza", "fuego", "aplauso"], true)) {
		http_response_code(422);
		echo json_encode(["ok" => false, "error" => "Reacción desconocida"]);
		exit;
	}

	$db = abrirComunidad();
	$borrar = $db->prepare("DELETE FROM reacciones WHERE id_publicacion = ? AND id_socio = ? AND tipo = ?");
	$borrar->execute([$publicacion, $yo, $tipo]);
	$puesta = false;
	if ($borrar->rowCount() === 0) {
		// No la tenía: la pongo. Si la publicación no existe, la clave ajena lo impide.
		try {
			$db->prepare("INSERT INTO reacciones (id_publicacion, id_socio, tipo) VALUES (?, ?, ?)")->execute([$publicacion, $yo, $tipo]);
			$puesta = true;
		} catch (PDOException $e) {
			http_response_code(404);
			echo json_encode(["ok" => false, "error" => "No existe la publicación"]);
			exit;
		}
	}

	$total = $db->prepare("SELECT COUNT(*) FROM reacciones WHERE id_publicacion = ? AND tipo = ?");
	$total->execute([$publicacion, $tipo]);
	echo json_encode(["ok" => true, "puesta" => $puesta, "total" => (int)$total->fetchColumn()]);
?>
