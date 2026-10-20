<?php
	// ¿Está libre este email? Responde {"valido": bool, "disponible": bool}
	header("Content-Type: application/json; charset=UTF-8");
	$email = trim($_GET['email'] ?? "");

	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		echo json_encode(["valido" => false, "disponible" => false]);
		exit;
	}

	$db = new PDO("sqlite:".__DIR__."/socios.db");
	$consulta = $db->prepare("SELECT COUNT(*) FROM socios WHERE lower(email) = lower(:email)");
	$consulta->execute([":email" => $email]);
	echo json_encode(["valido" => true, "disponible" => $consulta->fetchColumn() == 0]);
?>
