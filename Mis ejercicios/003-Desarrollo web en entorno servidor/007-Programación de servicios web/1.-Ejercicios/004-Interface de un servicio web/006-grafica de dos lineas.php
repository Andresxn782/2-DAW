<?php
	// Gráfica SVG con las temperaturas máximas y mínimas de la semana
	// y, debajo de las líneas, barras con la probabilidad de lluvia.
	include "aemet.php";
	$xml = descargarPrediccion($MUNICIPIO);

	// Arrays donde guardo los datos
	$fechas = [];
	$maximas = [];
	$minimas = [];
	$lluvias = [];

	foreach ($xml->prediccion->dia as $dia) {
		$fechas[] = substr((string)$dia["fecha"], 5);	// solo mes-día
		$maximas[] = (int)$dia->temperatura->maxima;
		$minimas[] = (int)$dia->temperatura->minima;
		$lluvia = 0;
		foreach ($dia->prob_precipitacion as $tramo) {
			if ((string)$tramo !== "") {
				$lluvia = max($lluvia, (int)$tramo);
			}
		}
		$lluvias[] = $lluvia;
	}

	// Medidas de la gráfica
	$ancho = 800;
	$alto = 400;
	$margenIzquierdo = 50;
	$margenSuperior = 30;
	$margenInferior = 50;
	$altoGrafica = $alto - $margenSuperior - $margenInferior;

	// Redondeo los límites a múltiplos de 5 para que las líneas de guía queden bonitas
	$temperaturaMaxima = (int)(ceil((max($maximas) + 3) / 5) * 5);
	$temperaturaMinima = (int)(floor((min($minimas) - 3) / 5) * 5);
	$rango = $temperaturaMaxima - $temperaturaMinima;
	$numeroDias = count($fechas);
	$separacionX = ($ancho - 100) / max(1, $numeroDias - 1);

	// Convierte una temperatura en la coordenada Y del dibujo
	function coordenadaY($temperatura) {
		global $margenSuperior, $temperaturaMaxima, $rango, $altoGrafica;
		return $margenSuperior + ($temperaturaMaxima - $temperatura) / $rango * $altoGrafica;
	}

	function puntos($valores) {
		global $margenIzquierdo, $separacionX;
		$texto = "";
		foreach ($valores as $i => $valor) {
			$texto .= ($margenIzquierdo + $i * $separacionX).",".coordenadaY($valor)." ";
		}
		return $texto;
	}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Temperaturas AEMET - Velnora</title>
	<style>
		body{font-family:Arial, sans-serif;background:#f7f7f2;}
		main{max-width:900px;margin:40px auto;background:white;padding:30px;border-radius:10px;}
		svg{width:100%;height:auto;border:1px solid #ddd;}
		.maxima{fill:none;stroke:#ea580c;stroke-width:3;}
		.minima{fill:none;stroke:#16a34a;stroke-width:3;}
		.lluvia{fill:#bfdbfe;}
		text{font-size:12px;fill:#555;}
	</style>
</head>
<body>
<main>
	<h1>Temperaturas en <?= $xml->nombre ?></h1>
	<p>Naranja: máxima · Verde: mínima · Azul: probabilidad de lluvia (la barra entera sería 100%).</p>
	<?php if (isset($xml->aviso_velnora)) { ?><p><em><?= $xml->aviso_velnora ?></em></p><?php } ?>

	<svg viewBox="0 0 <?= $ancho ?> <?= $alto ?>">
		<?php
			// Barras de lluvia
			foreach ($lluvias as $i => $lluvia) {
				$alturaBarra = $lluvia / 100 * $altoGrafica;
				$x = $margenIzquierdo + $i * $separacionX - 12;
				echo '<rect class="lluvia" x="'.$x.'" y="'.($margenSuperior + $altoGrafica - $alturaBarra).'" width="24" height="'.$alturaBarra.'"/>';
			}
			// Líneas horizontales de guía
			for ($t = $temperaturaMinima; $t <= $temperaturaMaxima; $t += 5) {
				$y = coordenadaY($t);
				echo '<line x1="'.$margenIzquierdo.'" y1="'.$y.'" x2="750" y2="'.$y.'" stroke="#ddd"/>';
				echo '<text x="10" y="'.($y + 4).'">'.$t.'º</text>';
			}
		?>
		<polyline class="maxima" points="<?= puntos($maximas) ?>"/>
		<polyline class="minima" points="<?= puntos($minimas) ?>"/>
		<?php
			// Puntos con su valor y la fecha debajo
			for ($i = 0; $i < $numeroDias; $i++) {
				$x = $margenIzquierdo + $i * $separacionX;
				echo '<circle cx="'.$x.'" cy="'.coordenadaY($maximas[$i]).'" r="5" fill="#ea580c"/>';
				echo '<text x="'.($x - 8).'" y="'.(coordenadaY($maximas[$i]) - 10).'">'.$maximas[$i].'</text>';
				echo '<circle cx="'.$x.'" cy="'.coordenadaY($minimas[$i]).'" r="5" fill="#16a34a"/>';
				echo '<text x="'.($x - 8).'" y="'.(coordenadaY($minimas[$i]) + 20).'">'.$minimas[$i].'</text>';
				echo '<text x="'.($x - 16).'" y="'.($alto - 20).'">'.$fechas[$i].'</text>';
			}
		?>
	</svg>
</main>
</body>
</html>
