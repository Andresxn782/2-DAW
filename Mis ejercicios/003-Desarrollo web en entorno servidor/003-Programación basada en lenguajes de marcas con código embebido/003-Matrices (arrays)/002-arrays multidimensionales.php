<?php
	// Cada posición del array es un socio, y cada socio tiene sus datos
	$socios = [];

	$socios[0]['nombre'] = "Lucía Martín";
	$socios[0]['telefono'] = "611223344";
	$socios[0]['email'] = "lucia@velnora.com";
	$socios[0]['plan'] = "Mensual";

	$socios[1]['nombre'] = "Marcos Gil";
	$socios[1]['telefono'] = "622334455";
	$socios[1]['email'] = "marcos@velnora.com";
	$socios[1]['plan'] = "Anual";

	var_dump($socios);
	echo "<br>";
	echo $socios[1]['nombre']." tiene el plan ".$socios[1]['plan'];
?>
