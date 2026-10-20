<?php
	// Panel de recepción de Velnora. Es otro programa distinto de la API:
	// no toca la base de datos, todo lo hace pidiéndoselo a la API con cURL.
	session_start();
	require __DIR__."/ClienteApi.php";

	// Dirección de la API calculada desde esta página (../api/index.php)
	$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
	$carpetaProyecto = dirname(dirname($_SERVER['SCRIPT_NAME']));
	$carpetaProyecto = $carpetaProyecto === "/" || $carpetaProyecto === "\\" ? "" : $carpetaProyecto;
	$base = $protocolo."://".$_SERVER['HTTP_HOST'].implode("/", array_map("rawurlencode", explode("/", $carpetaProyecto)))."/api/index.php";

	// La recepción usa la clave de administración
	$api = new ClienteApi($base, "velnora_admin_2026");

	if (empty($_SESSION['token'])) {
		$_SESSION['token'] = bin2hex(random_bytes(16));
	}

	// Acciones: siempre por POST, con token, y después redirijo (PRG)
	if ($_SERVER['REQUEST_METHOD'] === "POST") {
		if (!hash_equals($_SESSION['token'], $_POST['token'] ?? "")) {
			http_response_code(400);
			exit("Token no válido");
		}
		$accion = $_POST['accion'] ?? "";
		$volverA = (int)($_POST['clase'] ?? 0);
		if ($accion === "reservar") {
			$resultado = $api->pedir("POST", "/clases/".(int)$_POST['clase']."/reservas", ["socio_id" => (int)$_POST['socio_id']]);
		} else if ($accion === "cancelar") {
			$resultado = $api->pedir("DELETE", "/reservas/".(int)$_POST['reserva']);
		} else if ($accion === "crear") {
			$resultado = $api->pedir("POST", "/clases", [
				"nombre" => $_POST['nombre'] ?? "",
				"monitor" => $_POST['monitor'] ?? "",
				"dia" => $_POST['dia'] ?? "",
				"hora" => $_POST['hora'] ?? "",
				"duracion" => $_POST['duracion'] ?? "",
				"aforo" => $_POST['aforo'] ?? ""
			]);
			$volverA = $resultado['datos']['id'] ?? 0;
		} else if ($accion === "borrar") {
			$resultado = $api->pedir("DELETE", "/clases/".(int)$_POST['clase']);
			$volverA = 0;
		} else {
			$resultado = ["codigo" => 0, "datos" => ["mensaje" => "Acción desconocida"]];
		}

		$correcto = $resultado['codigo'] >= 200 && $resultado['codigo'] < 300;
		$mensaje = $correcto ? "Hecho." : ($resultado['datos']['mensaje'] ?? "Error al llamar a la API");
		if (!$correcto && !empty($resultado['datos']['errores'])) {
			$detalles = [];
			foreach ($resultado['datos']['errores'] as $campo => $texto) {
				$detalles[] = $campo.": ".$texto;
			}
			$mensaje .= " (".implode("; ", $detalles).")";
		}
		$_SESSION['aviso'] = ["correcto" => $correcto, "texto" => $mensaje];
		$_SESSION['ultima'] = $api->ultima;
		header("Location: index.php".($volverA > 0 ? "?clase=".$volverA : ""));
		exit;
	}

	$aviso = $_SESSION['aviso'] ?? null;
	$ultimaAccion = $_SESSION['ultima'] ?? null;
	unset($_SESSION['aviso'], $_SESSION['ultima']);

	$diaFiltro = $_GET['dia'] ?? "";
	$listado = $api->pedir("GET", "/clases", null, $diaFiltro !== "" ? ["dia" => $diaFiltro] : []);
	$socios = $api->pedir("GET", "/socios")['datos']['socios'] ?? [];
	$detalle = null;
	if (isset($_GET['clase'])) {
		$detalle = $api->pedir("GET", "/clases/".(int)$_GET['clase']."/reservas");
	}
	// Lo que enseño en "Intercambio con la API": la acción si la hubo, si no la última consulta
	$intercambio = $ultimaAccion ?? $api->ultima;
	$dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];

	function e($texto) {
		return htmlspecialchars((string)$texto);
	}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Recepción - Velnora</title>
	<link rel="stylesheet" href="estilo.css">
</head>
<body>
	<header>
		<strong>Velnora</strong> · Recepción
		<nav><a href="index.php">Clases</a> <a href="../docs/index.php">Documentación de la API</a></nav>
	</header>
	<main>
		<?php if ($aviso) { ?>
			<p class="aviso <?= $aviso['correcto'] ? "ok" : "mal" ?>"><?= e($aviso['texto']) ?></p>
		<?php } ?>

		<?php if ($listado['codigo'] !== 200) { ?>
			<p class="aviso mal">No he podido hablar con la API (código <?= $listado['codigo'] ?>). Comprueba la dirección: <?= e($base) ?></p>
		<?php } else { ?>
		<section class="caja">
			<h1>Clases de la semana</h1>
			<form class="filtros">
				<select name="dia">
					<option value="">Todos los días</option>
					<?php foreach ($dias as $dia) { ?>
						<option <?= $dia === $diaFiltro ? "selected" : "" ?>><?= $dia ?></option>
					<?php } ?>
				</select>
				<button>Filtrar</button>
			</form>
			<table>
				<tr><th>Clase</th><th>Monitor</th><th>Día y hora</th><th>Plazas</th><th></th></tr>
				<?php foreach ($listado['datos']['clases'] as $clase) { ?>
					<tr>
						<td><a href="?clase=<?= $clase['id'] ?>"><?= e($clase['nombre']) ?></a></td>
						<td><?= e($clase['monitor']) ?></td>
						<td><?= e($clase['dia']) ?> <?= e($clase['hora']) ?> (<?= $clase['duracion'] ?> min)</td>
						<td>
							<div class="barra"><span style="width:<?= round($clase['ocupadas'] / $clase['aforo'] * 100) ?>%"></span></div>
							<?= $clase['libres'] ?> libres de <?= $clase['aforo'] ?>
						</td>
						<td>
							<form method="post" onsubmit="return confirm('¿Borrar la clase y sus reservas?')">
								<input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
								<input type="hidden" name="accion" value="borrar">
								<input type="hidden" name="clase" value="<?= $clase['id'] ?>">
								<button class="enlace">Borrar</button>
							</form>
						</td>
					</tr>
				<?php } ?>
			</table>
		</section>
		<?php } ?>

		<?php if ($detalle !== null && $detalle['codigo'] === 200) {
			$clase = $detalle['datos']['clase'];
		?>
			<section class="caja">
				<h2><?= e($clase['nombre']) ?> · <?= e($clase['dia']) ?> <?= e($clase['hora']) ?></h2>
				<p>Plazas ocupadas: <?= $clase['ocupadas'] ?> de <?= $clase['aforo'] ?>.</p>
				<ul>
					<?php foreach ($detalle['datos']['reservas'] as $reserva) { ?>
						<li>
							<?= e($reserva['socio']) ?>
							<form method="post" class="en-linea">
								<input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
								<input type="hidden" name="accion" value="cancelar">
								<input type="hidden" name="clase" value="<?= $clase['id'] ?>">
								<input type="hidden" name="reserva" value="<?= $reserva['id'] ?>">
								<button class="enlace">Cancelar</button>
							</form>
						</li>
					<?php } ?>
				</ul>
				<form method="post" class="filtros">
					<input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
					<input type="hidden" name="accion" value="reservar">
					<input type="hidden" name="clase" value="<?= $clase['id'] ?>">
					<select name="socio_id">
						<?php foreach ($socios as $socio) { ?>
							<option value="<?= $socio['id'] ?>"><?= e($socio['nombre']) ?></option>
						<?php } ?>
					</select>
					<button>Reservar plaza</button>
				</form>
			</section>
		<?php } else if ($detalle !== null) { ?>
			<p class="aviso mal"><?= e($detalle['datos']['mensaje'] ?? "No se ha podido cargar la clase") ?></p>
		<?php } ?>

		<section class="caja">
			<h2>Nueva clase</h2>
			<form method="post" class="rejilla">
				<input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
				<input type="hidden" name="accion" value="crear">
				<label>Nombre <input name="nombre" maxlength="40"></label>
				<label>Monitor <input name="monitor" maxlength="40" value="Andres Ruiz"></label>
				<label>Día
					<select name="dia">
						<?php foreach ($dias as $dia) { ?><option><?= $dia ?></option><?php } ?>
					</select>
				</label>
				<label>Hora <input name="hora" type="time" value="18:00"></label>
				<label>Duración (min) <input name="duracion" type="number" value="60"></label>
				<label>Aforo <input name="aforo" type="number" value="15"></label>
				<button>Crear clase</button>
			</form>
		</section>

		<?php if ($intercambio) { ?>
			<section class="caja">
				<h2>Intercambio con la API</h2>
				<p><code><?= e($intercambio['metodo']) ?> <?= e($intercambio['url']) ?></code> → código <strong><?= $intercambio['codigo'] ?></strong></p>
				<?php if ($intercambio['cuerpo'] !== null) { ?>
					<p>Cuerpo enviado:</p>
					<pre><?= e(json_encode($intercambio['cuerpo'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
				<?php } ?>
				<p>Respuesta:</p>
				<pre><?= e($intercambio['respuesta']) ?></pre>
			</section>
		<?php } ?>
	</main>
	<footer>Velnora · Andres Ruiz</footer>
</body>
</html>
