<?php
	// Leo la predicción con SimpleXML: cada etiqueta es una propiedad
	// y cada atributo se lee como si fuera un array.
	include "aemet.php";
	$xml = descargarPrediccion($MUNICIPIO);

	echo "<h1>".$xml->nombre." (".$xml->provincia.")</h1>";
	echo "<p>Elaborado: ".$xml->elaborado."</p>";
	if (isset($xml->aviso_velnora)) {
		echo "<p><em>".$xml->aviso_velnora."</em></p>";
	}

	// El primer día: probabilidad de lluvia por tramos
	$hoy = $xml->prediccion->dia[0];
	echo "<h2>Lluvia el ".$hoy["fecha"]."</h2><ul>";
	foreach ($hoy->prob_precipitacion as $tramo) {
		$valor = (string)$tramo === "" ? "sin dato" : $tramo."%";
		echo "<li>".$tramo["periodo"]." h: ".$valor."</li>";
	}
	echo "</ul>";

	echo "<h2>Así es el objeto por dentro</h2><pre>";
	var_dump($hoy->temperatura);
	echo "</pre>";
?>
