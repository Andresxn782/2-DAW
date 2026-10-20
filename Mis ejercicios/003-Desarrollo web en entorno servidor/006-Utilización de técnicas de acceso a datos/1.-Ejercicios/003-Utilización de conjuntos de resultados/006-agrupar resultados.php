<?php
	$db = new PDO('sqlite:socios.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Opción A: que lo agrupe la base de datos
	$porPlan = $db->query("SELECT plan, COUNT(*) AS total FROM socios GROUP BY plan ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);

	// Opción B: traer las filas y agruparlas en PHP (útil cuando necesito los nombres)
	$grupos = [];
	foreach($db->query("SELECT nombre, plan FROM socios ORDER BY nombre", PDO::FETCH_ASSOC) as $fila){
		$grupos[$fila['plan']][] = $fila['nombre'];
	}
	$maximo = max(array_column($porPlan, 'total'));
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Socios por plan</title>
		<style>
			body{font-family:Arial, sans-serif;margin:30px;}
			.fila{display:flex;align-items:center;gap:10px;margin:6px 0;}
			.nombre{width:110px;}
			.barra{background:#0f766e;color:white;padding:4px 8px;border-radius:4px;}
			small{color:#555;}
		</style>
	</head>
	<body>
		<h1>Socios por plan</h1>
		<?php foreach($porPlan as $p){ ?>
			<div class="fila">
				<span class="nombre"><?php echo htmlspecialchars($p['plan']); ?></span>
				<span class="barra" style="width:<?php echo round($p['total'] / $maximo * 300); ?>px"><?php echo $p['total']; ?></span>
				<small><?php echo htmlspecialchars(implode(", ", $grupos[$p['plan']])); ?></small>
			</div>
		<?php } ?>
	</body>
</html>
