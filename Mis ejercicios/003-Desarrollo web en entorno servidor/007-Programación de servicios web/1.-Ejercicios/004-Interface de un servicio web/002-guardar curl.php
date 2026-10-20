<?php
	// Igual que el 001, pero además guardo la respuesta en un archivo.
	$curl = curl_init("https://www.aemet.es/xml/municipios/localidad_28079.xml");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	$respuesta = curl_exec($curl);
	$error = curl_error($curl);
	curl_close($curl);

	if ($respuesta === false) {
		echo "No he podido descargar la predicción: ".htmlspecialchars($error);
		exit;
	}

	$archivo = fopen(__DIR__."/prediccion.xml", "w");
	fwrite($archivo, $respuesta);
	fclose($archivo);

	echo "Guardados ".strlen($respuesta)." bytes en prediccion.xml";
?>
