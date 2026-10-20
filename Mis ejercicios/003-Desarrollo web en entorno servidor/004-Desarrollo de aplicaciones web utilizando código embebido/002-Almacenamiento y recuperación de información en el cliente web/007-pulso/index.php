<?php
	// Tablas que existen en la base de datos (menos la interna de SQLite)
	$db = new SQLite3('gimnasio.db');
	$result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT IN ('sqlite_sequence','usuarios','roles')");
	$tablas = [];
	while ($fila = $result->fetchArray(SQLITE3_ASSOC)) {
		$tablas[] = $fila['name'];
	}
	$db->close();

	// Solo acepto una tabla de la lista; si no, la primera
	$tabla = (isset($_GET['tabla']) && in_array($_GET['tabla'], $tablas)) ? $_GET['tabla'] : $tablas[0];
?>
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Pulso · Velnora</title>
		<link rel="stylesheet" href="css/estilo.css">
	</head>
	<body>
		<header>
			<h1>PULSO · VELNORA</h1>
		</header>
		<main>
			<nav>
				<?php include "includes/navegacion.php"; ?>
			</nav>
			<section>
				<?php include "includes/formulario.php"; ?>
				<?php include "includes/tabla.php"; ?>
			</section>
		</main>
	</body>
</html>
