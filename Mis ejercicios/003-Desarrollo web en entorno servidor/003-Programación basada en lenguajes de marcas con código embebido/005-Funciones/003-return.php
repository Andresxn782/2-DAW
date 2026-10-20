<?php
	// Devuelve el índice de masa corporal en vez de escribirlo
	function calcularIMC($peso, $altura){
		return round($peso / ($altura * $altura), 1);
	}

	$imc = calcularIMC(72, 1.78);
	echo "Tu IMC es ".$imc;
?>
