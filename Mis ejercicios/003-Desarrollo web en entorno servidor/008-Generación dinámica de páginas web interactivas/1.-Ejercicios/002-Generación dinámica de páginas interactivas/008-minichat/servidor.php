<?php
	// Servidor completo del chat.
	//   GET  servidor.php?accion=get&desde=15  -> mensajes con id mayor que 15
	//   POST servidor.php?accion=post  (usuario, mensaje) -> guarda un mensaje
	header("Content-Type: application/json; charset=UTF-8");
	$accion = $_GET['accion'] ?? "";

	try {
		$db = new PDO("sqlite:".__DIR__."/chat.db");
		// Que PDO lance excepciones si algo falla
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec("CREATE TABLE IF NOT EXISTS mensajes (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			usuario TEXT,
			fecha TEXT,
			mensaje TEXT
		)");

		switch ($accion) {
			case "get":
				// Solo los mensajes nuevos: así no se descarga todo cada segundo
				$desde = (int)($_GET['desde'] ?? 0);
				$consulta = $db->prepare("SELECT * FROM mensajes WHERE id > :desde ORDER BY id LIMIT 100");
				$consulta->execute([":desde" => $desde]);
				echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
				break;

			case "post":
				if ($_SERVER['REQUEST_METHOD'] !== "POST") {
					http_response_code(405);
					echo json_encode(["ok" => false, "error" => "Usa POST para enviar"]);
					break;
				}
				$usuario = trim($_POST['usuario'] ?? "");
				$mensaje = trim($_POST['mensaje'] ?? "");
				// La validación buena es la del servidor: el cliente se puede saltar
				if ($usuario === "" || mb_strlen($usuario) > 30) {
					http_response_code(422);
					echo json_encode(["ok" => false, "error" => "El nombre debe tener entre 1 y 30 caracteres"], JSON_UNESCAPED_UNICODE);
					break;
				}
				if ($mensaje === "" || mb_strlen($mensaje) > 200) {
					http_response_code(422);
					echo json_encode(["ok" => false, "error" => "El mensaje debe tener entre 1 y 200 caracteres"], JSON_UNESCAPED_UNICODE);
					break;
				}
				$fecha = date("Y-m-d H:i:s");
				$consulta = $db->prepare("INSERT INTO mensajes (usuario, fecha, mensaje) VALUES (:usuario, :fecha, :mensaje)");
				$consulta->execute([":usuario" => $usuario, ":fecha" => $fecha, ":mensaje" => $mensaje]);
				echo json_encode(["ok" => true, "id" => (int)$db->lastInsertId(), "fecha" => $fecha]);
				break;

			default:
				http_response_code(400);
				echo json_encode(["ok" => false, "error" => "Acción no válida"], JSON_UNESCAPED_UNICODE);
		}
	} catch (PDOException $e) {
		http_response_code(500);
		echo json_encode(["ok" => false, "error" => "Error de la base de datos"]);
	}
?>
