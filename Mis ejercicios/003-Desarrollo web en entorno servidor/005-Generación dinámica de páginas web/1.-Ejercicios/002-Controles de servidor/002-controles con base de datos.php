<?php
	// Los mismos controles, pero las opciones salen de la base de datos
	include "controles.php";

	$db = new PDO('sqlite:socios.db');

	// Opciones del desplegable: un socio por línea
	$socios = [];
	foreach($db->query("SELECT id, nombre, apellidos FROM socios ORDER BY nombre") as $fila){
		$socios[$fila['id']] = $fila['nombre']." ".$fila['apellidos'];
	}

	// Zonas del material sin repetir
	$zonas = [];
	foreach($db->query("SELECT DISTINCT zona FROM material ORDER BY zona") as $fila){
		$zonas[$fila['zona']] = $fila['zona'];
	}

	$zonaElegida = $_GET['zona'] ?? array_key_first($zonas);
	$consulta = $db->prepare("SELECT nombre, unidades, estado FROM material WHERE zona = :zona");
	$consulta->execute([':zona' => $zonaElegida]);
	$material = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Controles con base de datos</title>
		<style>body{font-family:Arial, sans-serif;margin:30px;} td, th{padding:5px 10px;border-bottom:1px solid #ccc;}</style>
	</head>
	<body>
		<h1>Préstamo de material</h1>
		<form method="GET">
			<p>Socio: <?php echo desplegable("socio", $socios, $_GET['socio'] ?? ""); ?></p>
			<p>Zona: <?php echo radios("zona", $zonas, $zonaElegida); ?></p>
			<input type="submit" value="Ver material">
		</form>
		<table>
			<tr><th>Material</th><th>Unidades</th><th>Estado</th></tr>
			<?php foreach($material as $m){ ?>
				<tr><td><?php echo htmlspecialchars($m['nombre']); ?></td><td><?php echo $m['unidades']; ?></td><td><?php echo htmlspecialchars($m['estado']); ?></td></tr>
			<?php } ?>
		</table>
	</body>
</html>
