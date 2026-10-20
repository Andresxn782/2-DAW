<?php
	// Cada línea del registro del torno tiene el formato:
	// 2026-10-19 07:12:44 | ENTRADA   | torno-1 | lucia
	$lineas = file("accesos.log", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	$porTipo = [];
	$porHora = [];
	$porSocio = [];
	$rechazos = [];
	foreach($lineas as $linea){
		$partes = array_map('trim', explode("|", $linea));
		if(count($partes) != 4){
			continue;		// Línea con formato raro: la salto
		}
		[$fecha, $tipo, $torno, $socio] = $partes;
		$porTipo[$tipo] = ($porTipo[$tipo] ?? 0) + 1;
		if($tipo == "ENTRADA"){
			$hora = substr($fecha, 11, 2);
			$porHora[$hora] = ($porHora[$hora] ?? 0) + 1;
			$porSocio[$socio] = ($porSocio[$socio] ?? 0) + 1;
		}
		if($tipo == "RECHAZADO"){
			$rechazos[] = $fecha." (".$socio.")";
		}
	}
	ksort($porHora);
	arsort($porSocio);

	echo "<h1>Accesos del torno</h1>";
	echo "<p>".count($lineas)." registros: ";
	foreach($porTipo as $tipo => $n){
		echo $tipo." ".$n." · ";
	}
	echo "</p><h2>Entradas por hora</h2>";
	foreach($porHora as $hora => $n){
		echo $hora.":00 ".str_repeat("■", $n)." ".$n."<br>";
	}
	echo "<h2>Socios que más vienen</h2><ol>";
	foreach(array_slice($porSocio, 0, 3, true) as $socio => $n){
		echo "<li>".htmlspecialchars($socio).": ".$n." entradas</li>";
	}
	echo "</ol><h2>Accesos rechazados</h2>";
	echo $rechazos ? implode("<br>", $rechazos) : "Ninguno";
?>
