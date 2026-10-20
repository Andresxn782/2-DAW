<?php
	// Documentación generada a partir de openapi.json, con un probador
	// para lanzar peticiones reales a la API desde el navegador.
	$api = json_decode(file_get_contents(__DIR__."/openapi.json"), true);

	function e($texto) {
		return htmlspecialchars((string)$texto);
	}

	// Saca un ejemplo de cuerpo a partir del esquema (para rellenar el probador)
	function ejemploCuerpo($api, $detalle) {
		$esquema = $detalle['requestBody']['content']['application/json']['schema'] ?? null;
		if ($esquema === null) {
			return "";
		}
		if (isset($esquema['$ref'])) {
			$esquema = $api['components']['schemas'][basename($esquema['$ref'])];
		}
		$ejemplo = [];
		foreach ($esquema['properties'] as $campo => $propiedad) {
			$ejemplo[$campo] = $propiedad['example'] ?? "";
		}
		return json_encode($ejemplo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	$metodos = ["get", "post", "put", "delete"];
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= e($api['info']['title']) ?></title>
	<link rel="stylesheet" href="../cliente/estilo.css">
	<style>
		.metodo{color:white;padding:3px 8px;border-radius:6px;font-weight:bold;text-transform:uppercase;font-size:13px;}
		.get{background:#2563eb;} .post{background:#16a34a;} .put{background:#ca8a04;} .delete{background:#b91c1c;}
		details{border:1px solid var(--borde);border-radius:10px;padding:10px 14px;margin-bottom:10px;background:white;}
		summary{cursor:pointer;}
		textarea{width:100%;min-height:110px;font-family:monospace;padding:8px;border:1px solid #d1d5db;border-radius:8px;}
	</style>
</head>
<body>
	<header>
		<strong>Velnora</strong> · Documentación de la API
		<nav><a href="../cliente/index.php">Panel de recepción</a> <a href="openapi.json">openapi.json</a></nav>
	</header>
	<main>
		<section class="caja">
			<h1><?= e($api['info']['title']) ?> <small>v<?= e($api['info']['version']) ?></small></h1>
			<p><?= e($api['info']['description']) ?></p>
			<p>Dirección: <code>…/velnora-api<?= e($api['servers'][0]['url']) ?>/clases</code> o <code>…/api/index.php?ruta=/clases</code>.</p>
			<p>Autenticación: cabecera <code>X-API-Key</code>. <?= e($api['components']['securitySchemes']['claveApi']['description']) ?>.</p>
			<p>Formato: JSON. Con <code>?formato=xml</code> o <code>Accept: application/xml</code> responde en XML.</p>
			<label>Clave para el probador <input id="clave" value="velnora_admin_2026"></label>
		</section>

		<?php foreach ($api['paths'] as $ruta => $operaciones) { ?>
			<?php foreach ($operaciones as $metodo => $detalle) {
				if (!in_array($metodo, $metodos, true)) {
					continue;
				}
				$parametros = array_merge($operaciones['parameters'] ?? [], $detalle['parameters'] ?? []);
			?>
				<details>
					<summary><span class="metodo <?= $metodo ?>"><?= $metodo ?></span> <code><?= e($ruta) ?></code> · <?= e($detalle['summary']) ?></summary>
					<?php if (count($parametros) > 0) { ?>
						<h3>Parámetros</h3>
						<ul>
							<?php foreach ($parametros as $parametro) { ?>
								<li><code><?= e($parametro['name']) ?></code> (<?= e($parametro['in']) ?>, <?= e($parametro['schema']['type']) ?><?= $parametro['required'] ? ", obligatorio" : "" ?>): <?= e($parametro['description']) ?></li>
							<?php } ?>
						</ul>
					<?php } ?>
					<h3>Respuestas</h3>
					<ul>
						<?php foreach ($detalle['responses'] as $codigo => $respuesta) { ?>
							<li><code><?= e($codigo) ?></code> <?= e($respuesta['description']) ?></li>
						<?php } ?>
					</ul>
					<h3>Probar</h3>
					<form class="probador" data-metodo="<?= strtoupper($metodo) ?>">
						<label>Ruta <input name="ruta" value="<?= e(str_replace("{id}", "1", $ruta)) ?>"></label>
						<?php $ejemplo = ejemploCuerpo($api, $detalle); if ($ejemplo !== "") { ?>
							<label>Cuerpo (JSON) <textarea name="cuerpo"><?= e($ejemplo) ?></textarea></label>
						<?php } ?>
						<button>Enviar <?= strtoupper($metodo) ?></button>
						<pre hidden></pre>
					</form>
				</details>
			<?php } ?>
		<?php } ?>
	</main>
	<script>
		// El probador llama a la API con fetch y enseña el código y la respuesta
		document.querySelectorAll(".probador").forEach(function (formulario) {
			formulario.addEventListener("submit", async function (evento) {
				evento.preventDefault();
				const salida = formulario.querySelector("pre");
				const opciones = {
					method: formulario.dataset.metodo,
					headers: { "X-API-Key": document.getElementById("clave").value, "Accept": "application/json" }
				};
				if (formulario.cuerpo) {
					opciones.headers["Content-Type"] = "application/json";
					opciones.body = formulario.cuerpo.value;
				}
				const [ruta, consulta] = formulario.ruta.value.split("?");
				const url = "../api/index.php?ruta=" + encodeURIComponent(ruta) + (consulta ? "&" + consulta : "");
				salida.hidden = false;
				try {
					const respuesta = await fetch(url, opciones);
					salida.textContent = opciones.method + " " + url + "\n" + respuesta.status + " " + respuesta.statusText + "\n\n" + await respuesta.text();
				} catch (error) {
					salida.textContent = "Error: " + error.message;
				}
			});
		});
	</script>
</body>
</html>
