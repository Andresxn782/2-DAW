<?php
	// ¿Cuánta memoria gasta leer accesos.csv de dos formas distintas?
	// Uso:  php 003-memoria.php
	require __DIR__."/AnalizadorAccesos.php";
	$archivo = __DIR__."/accesos.csv";
	if (!file_exists($archivo)) {
		exit("Primero genera los datos: php \"002-generar accesos.php\"\n");
	}
	$salto = PHP_SAPI === "cli" ? "\n" : "<br>";
	echo "Archivo: ".round(filesize($archivo) / 1048576, 1)." MB".$salto;

	// Forma 1: generador, una fila cada vez
	$antes = memory_get_usage();
	$filas = 0;
	foreach ((new AnalizadorAccesos($archivo))->filas() as $fila) {
		$filas++;
	}
	echo "Con generador: ".number_format($filas, 0, ",", ".")." filas, memoria extra máxima ".round((memory_get_peak_usage() - $antes) / 1024)." KB".$salto;

	// Forma 2: file() mete todas las líneas en un array
	memory_reset_peak_usage();
	$antes = memory_get_usage();
	$lineas = file($archivo);
	echo "Con file(): ".number_format(count($lineas), 0, ",", ".")." líneas, memoria extra ".round((memory_get_peak_usage() - $antes) / 1048576, 1)." MB".$salto;
	echo "Con 10 veces más datos, el generador seguiría gastando lo mismo y file() 10 veces más.".$salto;
?>
