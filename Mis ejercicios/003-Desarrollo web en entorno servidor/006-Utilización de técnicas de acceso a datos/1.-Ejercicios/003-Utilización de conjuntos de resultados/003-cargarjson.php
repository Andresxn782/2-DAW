<?php
	// Leo el JSON y lo convierto en un array asociativo (true = arrays, no objetos)
	$json = file_get_contents("gimnasio.json");
	$gimnasio = json_decode($json, true);

	if($gimnasio === null){
		die("El JSON tiene un error: ".json_last_error_msg());
	}

	echo "<h1>".htmlspecialchars($gimnasio['nombre'])." · ".htmlspecialchars($gimnasio['lema'])."</h1>";

	// Recorro un nivel anidado
	echo "<h2>Planes</h2><ul>";
	foreach($gimnasio['planes'] as $plan){
		$alMes = $plan['precio'] / $plan['meses'];
		echo "<li>".$plan['nombre'].": ".number_format($plan['precio'], 2, ',', '.')." € (".number_format($alMes, 2, ',', '.')." €/mes)</li>";
	}
	echo "</ul>";

	// Dos niveles: monitores y sus especialidades
	echo "<h2>Monitores</h2><ul>";
	foreach($gimnasio['monitores'] as $monitor){
		echo "<li>".$monitor['nombre'].": ".implode(", ", $monitor['especialidades'])."</li>";
	}
	echo "</ul>";

	// Las instalaciones no tienen todas los mismos campos: compruebo cada uno
	echo "<h2>Instalaciones</h2><ul>";
	foreach($gimnasio['instalaciones'] as $sala){
		$detalle = [];
		foreach($sala as $clave => $valor){
			if($clave != "sala"){
				$detalle[] = $clave.": ".(is_array($valor) ? implode(", ", $valor) : $valor);
			}
		}
		echo "<li><strong>".$sala['sala']."</strong> (".implode(" · ", $detalle).")</li>";
	}
	echo "</ul>";
?>
