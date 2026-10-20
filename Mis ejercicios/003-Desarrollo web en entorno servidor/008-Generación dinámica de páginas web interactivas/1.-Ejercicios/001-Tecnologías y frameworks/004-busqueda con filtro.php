<?php
	// Devuelve en JSON los ejercicios que contienen el texto de ?clave=
	// Uso una consulta preparada: si concatenara $_GET['clave'] en el SQL,
	// alguien podría escribir  ' OR 1=1 --  y hacer lo que quisiera.
	header("Content-Type: application/json; charset=UTF-8");

	$clave = trim($_GET['clave'] ?? "");
	if (mb_strlen($clave) < 2) {
		// Con menos de dos letras no busco: saldría casi todo
		echo "[]";
		exit;
	}

	$db = new PDO("sqlite:".__DIR__."/ejercicios.db");
	$consulta = $db->prepare("SELECT nombre, grupo, material FROM ejercicios WHERE nombre LIKE :clave ORDER BY nombre LIMIT 10");
	$consulta->execute([":clave" => "%".$clave."%"]);

	echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
?>
