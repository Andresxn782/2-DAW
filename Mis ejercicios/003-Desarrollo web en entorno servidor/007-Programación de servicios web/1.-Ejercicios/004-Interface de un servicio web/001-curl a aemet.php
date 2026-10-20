<?php
	// Pido la predicción de Madrid a la AEMET y la muestro tal cual llega.
	$curl = curl_init("https://www.aemet.es/xml/municipios/localidad_28079.xml");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	$respuesta = curl_exec($curl);
	curl_close($curl);

	// Le digo al navegador que es XML para que lo pinte como árbol
	header("Content-Type: application/xml");
	echo $respuesta;
?>
