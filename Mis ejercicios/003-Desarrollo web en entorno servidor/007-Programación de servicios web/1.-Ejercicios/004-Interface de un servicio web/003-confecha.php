<?php
	// Guardo cada descarga con la fecha y la hora en el nombre,
	// así voy acumulando un histórico de predicciones.
	$fecha = date('YmdHis');

	$curl = curl_init("https://www.aemet.es/xml/municipios/localidad_28079.xml");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	$respuesta = curl_exec($curl);
	curl_close($curl);

	if ($respuesta === false) {
		echo "No he podido descargar la predicción.";
		exit;
	}

	$nombre = "predicciones/prediccion".$fecha.".xml";
	file_put_contents(__DIR__."/".$nombre, $respuesta);

	echo "Guardado: ".$nombre."<br>";
	echo "Predicciones guardadas hasta ahora: ".count(glob(__DIR__."/predicciones/*.xml"));
?>
