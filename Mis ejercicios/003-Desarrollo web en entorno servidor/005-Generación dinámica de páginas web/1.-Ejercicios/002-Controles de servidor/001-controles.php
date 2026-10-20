<?php
	include "controles.php";

	$planes = ["musculacion" => "Musculación", "spinning" => "Spinning", "yoga" => "Yoga", "crossfit" => "Crossfit"];
	$extras = ["toalla" => "Toalla", "taquilla" => "Taquilla", "nutricion" => "Plan de nutrición"];
	$horarios = ["manana" => "Mañana", "tarde" => "Tarde", "noche" => "Noche"];

	$plan = $_POST['plan'] ?? "";
	$extrasElegidos = $_POST['extras'] ?? [];
	$horario = $_POST['horario'] ?? "tarde";
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Controles de servidor</title>
		<style>body{font-family:Arial, sans-serif;margin:30px;} p{margin:15px 0;}</style>
	</head>
	<body>
		<h1>Inscripción Velnora</h1>
		<form method="POST">
			<p>Plan: <?php echo desplegable("plan", $planes, $plan); ?></p>
			<p>Extras: <?php echo casillas("extras", $extras, $extrasElegidos); ?></p>
			<p>Horario: <?php echo radios("horario", $horarios, $horario); ?></p>
			<input type="submit" value="Enviar">
		</form>
		<?php if(!empty($_POST)){ ?>
			<p>Has elegido <strong><?php echo htmlspecialchars($planes[$plan] ?? "ningún plan"); ?></strong>
			por la <?php echo htmlspecialchars($horarios[$horario] ?? ""); ?>
			con <?php echo count($extrasElegidos); ?> extras. El formulario recuerda lo que marcaste.</p>
		<?php } ?>
	</body>
</html>
