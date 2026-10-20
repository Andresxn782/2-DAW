<?php
	// Lo que llega de un formulario siempre es texto
	$peso = "72";
	var_dump($peso);
	echo "<br>";
	// Al multiplicar, PHP lo convierte solo a número
	$peso = $peso * 1;
	var_dump($peso);
?>
