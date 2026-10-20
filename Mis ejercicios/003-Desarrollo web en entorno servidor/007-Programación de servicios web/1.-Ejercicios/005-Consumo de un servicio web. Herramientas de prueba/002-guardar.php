<?php
	// Script pensado para la consola (cron o Programador de tareas).
	// Uso __DIR__ porque cron no arranca en esta carpeta.
	$fecha = date('YmdHis');

	$curl = curl_init("https://www.aemet.es/xml/municipios/localidad_28079.xml");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 20);
	$respuesta = curl_exec($curl);
	$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if ($respuesta === false || $codigo !== 200) {
		// Devuelvo un código de salida distinto de 0 para que se note el fallo
		fwrite(STDERR, date('c')." Error al descargar (HTTP ".$codigo.")\n");
		exit(1);
	}

	file_put_contents(__DIR__."/predicciones/prediccion".$fecha.".xml", $respuesta);
	echo date('c')." Guardado: prediccion".$fecha.".xml\n";
?>
