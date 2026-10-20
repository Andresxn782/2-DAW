<?php
	// Función que reutilizo en los ejercicios 004, 005 y 006.
	// Descarga la predicción de la AEMET y la devuelve como SimpleXML.
	// Si no hay conexión, usa la copia de ejemplo para poder seguir trabajando.

	$MUNICIPIO = "28079";	// Madrid

	function descargarPrediccion($municipio) {
		$curl = curl_init("https://www.aemet.es/xml/municipios/localidad_".$municipio.".xml");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		$respuesta = curl_exec($curl);
		$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$xml = false;
		if ($respuesta !== false && $codigo === 200) {
			// La @ evita el aviso si lo que llega no es XML válido
			$xml = @simplexml_load_string($respuesta);
		}
		if ($xml === false) {
			$xml = simplexml_load_file(__DIR__."/prediccion-ejemplo.xml");
			$xml->addChild("aviso_velnora", "Sin conexión con la AEMET: datos de ejemplo");
		}
		return $xml;
	}
?>
