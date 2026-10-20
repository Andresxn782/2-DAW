<?php
	// Las clases de hoy las pinta el servidor directamente (son nuestras y rápidas).
	// El resto de tarjetas las rellena js/hub.js pidiéndolas a api.php una a una.
	require __DIR__."/inicio.php";
	$clases = consultar("clases", $config, $cache)['datos'];

	function e($texto) {
		return htmlspecialchars((string)$texto);
	}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Velnora Hub</title>
	<link rel="icon" href="data:,">
	<link rel="stylesheet" href="css/estilo.css">
	<script src="libs/chart-4.4.1.umd.js" defer></script>
	<script src="js/hub.js" defer></script>
</head>
<body>
	<header>
		<h1>Velnora <span>Hub</span></h1>
		<p>Todo lo que necesitas para entrenar hoy, en una pantalla.</p>
	</header>
	<main>
		<section class="tarjeta" id="tarjetaClases">
			<h2>Clases de hoy · <?= e($clases['hoy']['dia']) ?></h2>
			<?php if (count($clases['hoy']['clases']) === 0) { ?>
				<p class="suave">Ya no quedan clases hoy. Mañana (<?= e($clases['manana']['dia']) ?>):</p>
			<?php } ?>
			<ul class="lista">
				<?php foreach (count($clases['hoy']['clases']) > 0 ? $clases['hoy']['clases'] : $clases['manana']['clases'] as $clase) { ?>
					<li class="<?= $clase['aire_libre'] ? "aire-libre" : "" ?>">
						<strong><?= e($clase['hora']) ?></strong> <?= e($clase['nombre']) ?>
						<span class="suave"><?= e($clase['sala']) ?> · <?= e($clase['monitor']) ?></span>
					</li>
				<?php } ?>
			</ul>
			<p class="origen">Fuente: base de datos de Velnora (SQLite)</p>
		</section>

		<section class="tarjeta" id="tarjetaTiempo" aria-busy="true">
			<h2>¿Entrenar fuera?</h2>
			<div class="contenido"><p class="suave">Consultando la AEMET...</p></div>
			<p class="origen"></p>
		</section>

		<section class="tarjeta" id="tarjetaAfluencia" aria-busy="true">
			<h2>¿Cuánta gente hay?</h2>
			<div class="contenido"><p class="suave">Analizando los accesos...</p></div>
			<canvas id="graficaAfluencia" height="160"></canvas>
			<p class="origen"></p>
		</section>

		<section class="tarjeta ancha" id="tarjetaRutas" aria-busy="true">
			<h2>Rutas para correr</h2>
			<div class="contenido rutas"><p class="suave">Cargando rutas...</p></div>
			<p class="origen"></p>
		</section>

		<section class="tarjeta ancha" id="tarjetaVideos" aria-busy="true">
			<h2>Velnora TV recomienda</h2>
			<div class="contenido"><p class="suave">Buscando vídeos...</p></div>
			<p class="origen"></p>
		</section>
	</main>
	<footer>Velnora Hub · Andres Ruiz · Datos del tiempo: © AEMET</footer>

	<template id="plantillaRuta">
		<article class="ruta">
			<svg viewBox="0 0 200 140" role="img"><polyline fill="none" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/></svg>
			<h3></h3>
			<p class="suave"></p>
		</article>
	</template>
	<template id="plantillaVideo">
		<article class="video">
			<img alt="" loading="lazy">
			<div><h3></h3><p class="suave"></p></div>
		</article>
	</template>
</body>
</html>
