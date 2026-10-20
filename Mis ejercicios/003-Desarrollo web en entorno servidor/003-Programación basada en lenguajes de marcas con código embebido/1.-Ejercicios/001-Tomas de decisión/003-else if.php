<?php
	// Clasifico las pulsaciones en zonas de entrenamiento
	$pulsaciones = 145;
	if($pulsaciones < 100){
		echo "Zona 1: calentamiento";
	}else if($pulsaciones >= 100 && $pulsaciones < 130){
		echo "Zona 2: quema de grasa";
	}else if($pulsaciones >= 130 && $pulsaciones < 150){
		echo "Zona 3: aeróbica";
	}else if($pulsaciones >= 150 && $pulsaciones < 170){
		echo "Zona 4: anaeróbica";
	}else{
		echo "Zona 5: esfuerzo máximo";
	}
?>
