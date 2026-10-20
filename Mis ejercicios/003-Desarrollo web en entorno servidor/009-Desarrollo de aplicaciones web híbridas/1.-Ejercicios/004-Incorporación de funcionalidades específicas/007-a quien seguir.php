<?php
	// "A quién seguir": gente a la que siguen las personas que yo sigo,
	// ordenada por cuántos de mis contactos la siguen. Excluyo a los que ya sigo y a mí.
	session_start();
	require __DIR__."/conexion.php";
	$db = abrirComunidad();
	$yo = $_SESSION['socio'] ?? 1;

	$consulta = $db->prepare("
		SELECT s.id, s.nombre, s.apellidos, s.plan, COUNT(*) AS en_comun,
			GROUP_CONCAT(intermedio.nombre, ', ') AS a_traves_de
		FROM seguimientos mios
		JOIN seguimientos suyos ON suyos.id_seguidor = mios.id_seguido
		JOIN socios s ON s.id = suyos.id_seguido
		JOIN socios intermedio ON intermedio.id = mios.id_seguido
		WHERE mios.id_seguidor = :yo
			AND suyos.id_seguido != :yo
			AND suyos.id_seguido NOT IN (SELECT id_seguido FROM seguimientos WHERE id_seguidor = :yo)
		GROUP BY s.id
		ORDER BY en_comun DESC, s.id
		LIMIT 8
	");
	$consulta->execute([":yo" => $yo]);
	$sugerencias = $consulta->fetchAll();
	$nombre = $db->query("SELECT nombre FROM socios WHERE id = ".(int)$yo)->fetchColumn();
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>A quién seguir - Comunidad Velnora</title>
	<style>
		body{font-family:"Segoe UI", Arial, sans-serif;max-width:620px;margin:20px auto;padding:0 16px;}
		li{margin-bottom:10px;}
		small{color:#6b7280;}
	</style>
</head>
<body>
	<p><a href="004-muro.php">← Volver al muro</a></p>
	<h1>Sugerencias para <?= htmlspecialchars($nombre) ?></h1>
	<ol>
		<?php foreach ($sugerencias as $socio) { ?>
			<li>
				<strong><?= htmlspecialchars($socio['nombre']." ".$socio['apellidos']) ?></strong> (<?= htmlspecialchars($socio['plan']) ?>)<br>
				<small>Le siguen <?= $socio['en_comun'] ?> de tus contactos: <?= htmlspecialchars($socio['a_traves_de']) ?></small>
			</li>
		<?php } ?>
	</ol>
	<?php if (count($sugerencias) === 0) { ?><p>No hay sugerencias todavía.</p><?php } ?>
</body>
</html>
