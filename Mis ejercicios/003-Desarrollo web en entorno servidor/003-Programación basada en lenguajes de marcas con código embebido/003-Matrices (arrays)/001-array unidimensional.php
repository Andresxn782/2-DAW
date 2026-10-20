<?php
	$ejercicios = ['sentadilla','press banca','peso muerto'];
	var_dump($ejercicios);
	echo "<br>";
	array_push($ejercicios,'dominadas');
	var_dump($ejercicios);
	echo "<br>";
	echo "El primer ejercicio es: ".$ejercicios[0];
	echo "<br>";
	echo "Hay ".count($ejercicios)." ejercicios en la rutina";
?>
