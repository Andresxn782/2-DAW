<?php
	// Genero la página de documentación a partir de openapi.json.
	$api = json_decode(file_get_contents(__DIR__."/openapi.json"), true);

	function e($texto) {
		return htmlspecialchars((string)$texto);
	}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title><?= e($api['info']['title']) ?></title>
	<style>
		body{font-family:Arial, sans-serif;max-width:900px;margin:30px auto;color:#1f2937;}
		.ruta{border:1px solid #ddd;border-radius:10px;padding:16px;margin-bottom:16px;}
		.metodo{background:#16a34a;color:white;padding:3px 8px;border-radius:6px;font-weight:bold;text-transform:uppercase;}
		table{border-collapse:collapse;width:100%;}
		th, td{border-bottom:1px solid #eee;padding:6px;text-align:left;}
		code{background:#f3f4f6;padding:2px 4px;border-radius:4px;}
	</style>
</head>
<body>
	<h1><?= e($api['info']['title']) ?> <small>v<?= e($api['info']['version']) ?></small></h1>
	<p><?= e($api['info']['description']) ?></p>
	<p>Dirección base: <code><?= e($api['servers'][0]['url']) ?></code></p>

	<h2>Autenticación</h2>
	<?php foreach ($api['components']['securitySchemes'] as $nombre => $esquema) { ?>
		<p>Cabecera <code><?= e($esquema['name']) ?></code> con la clave que da el gimnasio.</p>
	<?php } ?>

	<h2>Rutas</h2>
	<?php foreach ($api['paths'] as $ruta => $metodos) { ?>
		<?php foreach ($metodos as $metodo => $detalle) { ?>
			<div class="ruta">
				<p><span class="metodo"><?= e($metodo) ?></span> <code><?= e($ruta) ?></code> · <?= e($detalle['summary']) ?></p>
				<p><?= e($detalle['description'] ?? "") ?></p>
				<?php if (!empty($detalle['parameters'])) { ?>
					<h3>Parámetros</h3>
					<table>
						<tr><th>Nombre</th><th>Dónde</th><th>Tipo</th><th>Obligatorio</th><th>Descripción</th></tr>
						<?php foreach ($detalle['parameters'] as $parametro) { ?>
							<tr>
								<td><code><?= e($parametro['name']) ?></code></td>
								<td><?= e($parametro['in']) ?></td>
								<td><?= e($parametro['schema']['type']) ?></td>
								<td><?= $parametro['required'] ? "Sí" : "No" ?></td>
								<td><?= e($parametro['description']) ?></td>
							</tr>
						<?php } ?>
					</table>
				<?php } ?>
				<h3>Respuestas</h3>
				<table>
					<?php foreach ($detalle['responses'] as $codigo => $respuesta) { ?>
						<tr><td><code><?= e($codigo) ?></code></td><td><?= e($respuesta['description']) ?></td></tr>
					<?php } ?>
				</table>
			</div>
		<?php } ?>
	<?php } ?>

	<h2>Modelos de datos</h2>
	<?php foreach ($api['components']['schemas'] as $nombre => $esquema) { ?>
		<h3><?= e($nombre) ?></h3>
		<table>
			<tr><th>Campo</th><th>Tipo</th><th>Ejemplo</th></tr>
			<?php foreach ($esquema['properties'] as $campo => $propiedad) { ?>
				<tr><td><code><?= e($campo) ?></code></td><td><?= e($propiedad['type']) ?></td><td><?= e(var_export($propiedad['example'], true)) ?></td></tr>
			<?php } ?>
		</table>
	<?php } ?>
</body>
</html>
