<?php
	// Página principal. El servidor la manda ya rellena con el estado actual
	// (sirve aunque JavaScript falle) y luego js/pizarra.js la mantiene al día.
	require __DIR__."/includes/funciones.php";

	$error = "";
	// Entrada sin JavaScript: un formulario normal que recarga la página
	if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['entrar'])) {
		$nombre = trim($_POST['nombre'] ?? "");
		$rol = ($_POST['rol'] ?? "") === "monitor" ? "monitor" : "socio";
		if (!preg_match('/^[\p{L} ]{2,30}$/u', $nombre)) {
			$error = "El nombre debe tener entre 2 y 30 letras";
		} else if ($rol === "monitor" && ($_POST['pin'] ?? "") !== PIN_MONITOR) {
			$error = "El PIN de monitor no es correcto";
		} else {
			session_regenerate_id(true);
			$_SESSION['usuario'] = ["nombre" => $nombre, "rol" => $rol];
			header("Location: index.php");
			exit;
		}
	}

	$usuario = usuario();
	if ($usuario !== null) {
		$db = conectar();
		$wod = wodActual($db);
		$ranking = ranking($db, $wod);
		$animos = animos($db, $wod);
		$miResultado = null;
		foreach ($ranking as $fila) {
			if ($fila['socio'] === $usuario['nombre']) {
				$miResultado = $fila;
			}
		}
	}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pizarra - Velnora</title>
	<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
	<header>
		<strong>Velnora</strong> · Pizarra del box
		<?php if ($usuario) { ?>
			<span class="quien"><?= e($usuario['nombre']) ?><?= $usuario['rol'] === "monitor" ? " (monitor)" : "" ?> · <a href="salir.php">Salir</a></span>
		<?php } ?>
	</header>

	<?php if ($usuario === null) { ?>
		<main class="estrecho">
			<form method="post" class="caja" id="formEntrar">
				<h1>Entra en la pizarra</h1>
				<?php if ($error) { ?><p class="error"><?= e($error) ?></p><?php } ?>
				<label>Tu nombre <input name="nombre" required minlength="2" maxlength="30" value="<?= e($_POST['nombre'] ?? "") ?>"></label>
				<label class="opcion"><input type="radio" name="rol" value="socio" checked> Socio</label>
				<label class="opcion"><input type="radio" name="rol" value="monitor"> Monitor</label>
				<label id="campoPin" hidden>PIN de monitor <input name="pin" type="password"></label>
				<button name="entrar" value="1">Entrar</button>
			</form>
		</main>
		<script>
			// Enseño el campo del PIN solo si se elige monitor
			document.querySelectorAll("[name=rol]").forEach(function(radio){
				radio.onchange = function(){
					document.querySelector("#campoPin").hidden = radio.value !== "monitor" || !radio.checked
				}
			})
		</script>
	<?php } else { ?>
		<main id="pizarra"
			data-token="<?= token() ?>"
			data-socio="<?= e($usuario['nombre']) ?>"
			data-wod="<?= $wod['id'] ?>"
			data-tipo="<?= e($wod['tipo']) ?>"
			data-animos-desde="<?= count($animos) > 0 ? end($animos)['id'] : 0 ?>">

			<section class="caja wod">
				<p class="etiqueta">WOD de hoy · <span id="wodMonitor"><?= e($wod['monitor']) ?></span></p>
				<h1 id="wodTitulo"><?= e($wod['titulo']) ?></h1>
				<p id="wodDescripcion"><?= e($wod['descripcion']) ?></p>
				<p class="etiqueta" id="wodTipo"><?= $wod['tipo'] === "tiempo" ? "Por tiempo: gana el más rápido" : "Por repeticiones: gana quien haga más" ?></p>
			</section>

			<div class="columnas">
				<section class="caja">
					<h2>Clasificación <span class="directo" id="directo">en directo</span></h2>
					<table>
						<thead><tr><th>#</th><th>Socio</th><th>Marca</th></tr></thead>
						<tbody id="ranking">
							<?php foreach ($ranking as $fila) { ?>
								<tr data-socio="<?= e($fila['socio']) ?>" class="<?= $fila['socio'] === $usuario['nombre'] ? "yo" : "" ?>">
									<td class="posicion"><?= $fila['posicion'] ?></td>
									<td class="nombre"><?= e($fila['socio']) ?></td>
									<td class="marca"><?= e($fila['texto']) ?><?= $fila['escalado'] ? ' <span class="escalado">escalado</span>' : "" ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<p id="sinResultados" <?= count($ranking) > 0 ? "hidden" : "" ?>>Todavía no hay resultados. ¡Sé el primero!</p>
				</section>

				<section class="caja">
					<h2>Tu resultado</h2>
					<form id="formResultado" novalidate>
						<label><span id="etiquetaValor"><?= $wod['tipo'] === "tiempo" ? "Tiempo (min:seg)" : "Repeticiones" ?></span>
							<input name="valor" id="valor" autocomplete="off"
								placeholder="<?= $wod['tipo'] === "tiempo" ? "6:45" : "120" ?>"
								inputmode="<?= $wod['tipo'] === "tiempo" ? "text" : "numeric" ?>"
								value="<?= $miResultado ? e($wod['tipo'] === "tiempo" ? $miResultado['texto'] : $miResultado['valor']) : "" ?>">
						</label>
						<small class="error" id="errorValor"></small>
						<label class="opcion"><input type="checkbox" name="escalado" <?= $miResultado && $miResultado['escalado'] ? "checked" : "" ?>> Lo he hecho escalado</label>
						<button>Guardar</button>
						<button type="button" class="secundario" id="borrarResultado" <?= $miResultado ? "" : "hidden" ?>>Quitar mi resultado</button>
					</form>
					<p id="mensajeResultado" aria-live="polite"></p>

					<h2>Ánimos</h2>
					<div id="animos">
						<?php foreach ($animos as $animo) { ?>
							<p class="animo"><strong><?= e($animo['socio']) ?></strong> <?= e($animo['texto']) ?> <time><?= e($animo['hora']) ?></time></p>
						<?php } ?>
					</div>
					<form id="formAnimo" class="en-linea" novalidate>
						<input name="texto" maxlength="140" placeholder="¡Vamos, que tú puedes!" autocomplete="off">
						<button>Enviar</button>
					</form>
					<small class="error" id="errorAnimo"></small>
				</section>
			</div>

			<?php if (esMonitor()) { ?>
				<section class="caja">
					<h2>Publicar un WOD nuevo</h2>
					<form id="formWod" novalidate>
						<label>Título <input name="titulo" maxlength="60" required></label>
						<label>Descripción <textarea name="descripcion" maxlength="300" required></textarea></label>
						<label class="opcion"><input type="radio" name="tipo" value="tiempo" checked> Por tiempo</label>
						<label class="opcion"><input type="radio" name="tipo" value="repeticiones"> Por repeticiones</label>
						<button>Publicar</button>
						<small class="error" id="errorWod"></small>
					</form>
				</section>
			<?php } ?>
		</main>
		<script src="js/pizarra.js"></script>
	<?php } ?>
</body>
</html>
