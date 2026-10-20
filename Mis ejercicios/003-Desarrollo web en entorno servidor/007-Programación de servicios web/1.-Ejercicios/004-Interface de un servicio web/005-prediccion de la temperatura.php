<?php
	// Para cada día decido si la clase de running de Velnora (18:00)
	// se hace fuera o en la cinta, según la lluvia y la temperatura.
	include "aemet.php";
	$xml = descargarPrediccion($MUNICIPIO);

	function lluviaDeTarde($dia) {
		// Busco el tramo 12-24 o, si no está, el 00-24
		$valor = "";
		foreach ($dia->prob_precipitacion as $tramo) {
			if ((string)$tramo["periodo"] === "12-24" && (string)$tramo !== "") {
				return (int)$tramo;
			}
			if ((string)$tramo["periodo"] === "00-24") {
				$valor = (string)$tramo;
			}
		}
		return $valor === "" ? 0 : (int)$valor;
	}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Running al aire libre - Velnora</title>
	<style>
		body{font-family:Arial, sans-serif;max-width:800px;margin:30px auto;}
		.dia{border:1px solid #ddd;border-radius:10px;padding:12px;margin-bottom:10px;}
		.fuera{border-left:6px solid #16a34a;}
		.dentro{border-left:6px solid #b91c1c;}
	</style>
</head>
<body>
	<h1>Running al aire libre en <?= $xml->nombre ?></h1>
	<?php if (isset($xml->aviso_velnora)) { ?><p><em><?= $xml->aviso_velnora ?></em></p><?php } ?>

	<?php foreach ($xml->prediccion->dia as $dia) {
		$maxima = (int)$dia->temperatura->maxima;
		$minima = (int)$dia->temperatura->minima;
		$lluvia = lluviaDeTarde($dia);
		$fuera = $lluvia < 40 && $maxima < 33 && $maxima > 5;
	?>
		<div class="dia <?= $fuera ? "fuera" : "dentro" ?>">
			<strong><?= $dia["fecha"] ?></strong>:
			máxima <?= $maxima ?> ºC, mínima <?= $minima ?> ºC, lluvia por la tarde <?= $lluvia ?>%.
			<br>
			<?= $fuera ? "La clase se hace en el parque." : "La clase se hace en las cintas del gimnasio." ?>
		</div>
	<?php } ?>
</body>
</html>
