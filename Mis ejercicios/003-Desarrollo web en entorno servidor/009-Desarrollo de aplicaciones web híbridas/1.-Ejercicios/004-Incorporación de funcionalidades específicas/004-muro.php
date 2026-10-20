<?php
	// Muro de un socio: publicaciones de la gente a la que sigue (y las suyas).
	// Para no hacer un login en este ejercicio, se elige "ver como" un socio y se guarda en la sesión.
	session_start();
	require __DIR__."/conexion.php";
	$db = abrirComunidad();

	if (isset($_GET['socio'])) {
		$_SESSION['socio'] = (int)$_GET['socio'];
	}
	$yo = $_SESSION['socio'] ?? 1;
	if (empty($_SESSION['token'])) {
		$_SESSION['token'] = bin2hex(random_bytes(16));
	}

	$socios = $db->query("SELECT id, nombre || ' ' || apellidos AS nombre FROM socios ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR);

	// Una sola consulta: cada publicación con el recuento de cada tipo de reacción
	// y si yo ya he puesto esa reacción
	$consulta = $db->prepare("
		SELECT p.id, p.fecha, p.texto, p.tipo, s.nombre, s.apellidos,
			SUM(r.tipo = 'fuerza') AS fuerza,
			SUM(r.tipo = 'fuego') AS fuego,
			SUM(r.tipo = 'aplauso') AS aplauso,
			GROUP_CONCAT(CASE WHEN r.id_socio = :yo THEN r.tipo END) AS mias
		FROM publicaciones p
		JOIN socios s ON s.id = p.id_socio
		LEFT JOIN reacciones r ON r.id_publicacion = p.id
		WHERE p.id_socio = :yo OR p.id_socio IN (SELECT id_seguido FROM seguimientos WHERE id_seguidor = :yo)
		GROUP BY p.id
		ORDER BY p.fecha DESC
		LIMIT 30
	");
	$consulta->execute([":yo" => $yo]);
	$publicaciones = $consulta->fetchAll();

	$sigo = $db->prepare("SELECT COUNT(*) FROM seguimientos WHERE id_seguidor = ?");
	$sigo->execute([$yo]);
	$meSiguen = $db->prepare("SELECT COUNT(*) FROM seguimientos WHERE id_seguido = ?");
	$meSiguen->execute([$yo]);

	$iconos = ["fuerza" => "💪", "fuego" => "🔥", "aplauso" => "👏"];
	$etiquetas = ["entreno" => "Entreno", "logro" => "Logro", "pregunta" => "Pregunta"];
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Comunidad Velnora</title>
	<style>
		body{font-family:"Segoe UI", Arial, sans-serif;background:#f3f4f6;margin:0;}
		header{background:#16a34a;color:white;padding:12px 16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;}
		header form{margin-left:auto;}
		header a{color:white;}
		main{max-width:620px;margin:0 auto;padding:16px;}
		.cifras{color:#4b5563;}
		article{background:white;border-radius:12px;padding:14px 16px;margin-bottom:12px;border:1px solid #e5e7eb;}
		.cabecera{display:flex;justify-content:space-between;gap:10px;font-size:14px;}
		.tipo{font-size:11px;text-transform:uppercase;background:#dcfce7;color:#166534;border-radius:6px;padding:2px 6px;}
		.tipo.logro{background:#fef3c7;color:#92400e;}
		.tipo.pregunta{background:#dbeafe;color:#1e40af;}
		time{color:#6b7280;font-size:13px;}
		.reacciones{display:flex;gap:8px;}
		.reacciones button{border:1px solid #e5e7eb;background:white;border-radius:20px;padding:4px 10px;cursor:pointer;font:inherit;}
		.reacciones button.puesta{background:#dcfce7;border-color:#16a34a;}
	</style>
</head>
<body>
	<header>
		<strong>Comunidad Velnora</strong>
		<a href="006-actividad.html">Actividad</a>
		<a href="007-a quien seguir.php">A quién seguir</a>
		<form>
			<label>Ver como
				<select name="socio" onchange="this.form.submit()">
					<?php foreach ($socios as $id => $nombre) { ?>
						<option value="<?= $id ?>" <?= $id === $yo ? "selected" : "" ?>><?= htmlspecialchars($nombre) ?></option>
					<?php } ?>
				</select>
			</label>
		</form>
	</header>
	<main data-token="<?= $_SESSION['token'] ?>">
		<h1>Hola, <?= htmlspecialchars(explode(" ", $socios[$yo])[0]) ?></h1>
		<p class="cifras">Sigues a <?= $sigo->fetchColumn() ?> socios y te siguen <?= $meSiguen->fetchColumn() ?>.</p>
		<?php foreach ($publicaciones as $publicacion) {
			$mias = explode(",", (string)$publicacion['mias']);
		?>
			<article>
				<div class="cabecera">
					<strong><?= htmlspecialchars($publicacion['nombre']." ".$publicacion['apellidos']) ?></strong>
					<span class="tipo <?= $publicacion['tipo'] ?>"><?= $etiquetas[$publicacion['tipo']] ?></span>
				</div>
				<time datetime="<?= $publicacion['fecha'] ?>"><?= haceCuanto($publicacion['fecha']) ?></time>
				<p><?= htmlspecialchars($publicacion['texto']) ?></p>
				<div class="reacciones">
					<?php foreach ($iconos as $tipo => $icono) { ?>
						<button data-publicacion="<?= $publicacion['id'] ?>" data-tipo="<?= $tipo ?>" class="<?= in_array($tipo, $mias, true) ? "puesta" : "" ?>" title="<?= $tipo ?>">
							<?= $icono ?> <span><?= (int)$publicacion[$tipo] ?></span>
						</button>
					<?php } ?>
				</div>
			</article>
		<?php } ?>
		<?php if (count($publicaciones) === 0) { ?>
			<p>Todavía no hay nada en tu muro. Mira <a href="007-a quien seguir.php">a quién seguir</a>.</p>
		<?php } ?>
	</main>
	<script>
		let token = document.querySelector("main").dataset.token
		// Un solo manejador para todos los botones (delegación de eventos)
		document.querySelector("main").addEventListener("click", function(evento){
			let boton = evento.target.closest(".reacciones button")
			if(!boton){
				return
			}
			let datos = new FormData()
			datos.append("publicacion", boton.dataset.publicacion)
			datos.append("tipo", boton.dataset.tipo)
			fetch("005-reaccionar.php", { method: "POST", headers: { "X-Token": token }, body: datos })
			.then(function(respuesta){ return respuesta.json() })
			.then(function(resultado){
				if(resultado.ok){
					boton.classList.toggle("puesta", resultado.puesta)
					boton.querySelector("span").textContent = resultado.total
				}
			})
		})
	</script>
</body>
</html>
