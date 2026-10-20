<?php
	// Cada vez que se abre la página añado una línea al registro de visitas
	// Con 'a' el archivo no se borra, se escribe al final
	$archivo = fopen("visitas.txt",'a');
	fwrite($archivo, date("Y-m-d H:i:s")." - ".$_SERVER['REMOTE_ADDR']."\n");
	fclose($archivo);

	echo "Visita guardada. Registro completo:<br>";
	echo nl2br(file_get_contents("visitas.txt"));
?>
