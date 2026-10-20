<?php
	// Leo el CSV fila a fila: cada fila llega como un array
	$archivo = fopen("datos.csv", "r");
	$conjunto = [];
	while(($fila = fgetcsv($archivo, null, ",", "\"", "\\")) !== false){
		$conjunto[] = $fila;
	}
	fclose($archivo);

	echo "<pre>";
	var_dump($conjunto[0], $conjunto[1]);
	echo "</pre>";
	echo count($conjunto) - 1 ." socios (la primera fila son las cabeceras)";
?>
