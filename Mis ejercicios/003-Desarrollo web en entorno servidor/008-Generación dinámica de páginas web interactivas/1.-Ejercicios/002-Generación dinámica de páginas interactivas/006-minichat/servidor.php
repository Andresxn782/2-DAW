<?php
	// Servidor del chat: de momento solo sabe devolver los mensajes.
	$accion = $_GET['accion'] ?? "";
	$db = new PDO("sqlite:".__DIR__."/chat.db");

	// La primera vez creo la tabla con dos mensajes de ejemplo
	$db->exec("CREATE TABLE IF NOT EXISTS mensajes (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		usuario TEXT,
		fecha TEXT,
		mensaje TEXT
	)");
	if ($db->query("SELECT COUNT(*) FROM mensajes")->fetchColumn() == 0) {
		$db->exec("INSERT INTO mensajes (usuario, fecha, mensaje) VALUES
			('Marcos', datetime('now', 'localtime'), '¿Alguien me ayuda con la barra?'),
			('Lucía', datetime('now', 'localtime'), 'Voy en un minuto')");
	}

	switch ($accion) {
		case "get":
			// Dame todos los mensajes
			$resultado = $db->query("SELECT * FROM mensajes ORDER BY id");
			$mensajes = [];
			while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
				$mensajes[] = $fila;
			}
			header("Content-Type: application/json; charset=UTF-8");
			echo json_encode($mensajes, JSON_UNESCAPED_UNICODE);
			break;
		case "post":
			// Te envío un mensaje (lo hago en el paso 008)
			break;
	}
?>
