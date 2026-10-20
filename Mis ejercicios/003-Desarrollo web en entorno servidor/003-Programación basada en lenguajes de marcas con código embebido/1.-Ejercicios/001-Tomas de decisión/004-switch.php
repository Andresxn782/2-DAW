<?php
	// Cada día de la semana el gimnasio Velnora propone una rutina distinta
	$diadelasemana = "miércoles";
	switch($diadelasemana){
		case "lunes":
			echo "Hoy toca pierna";
			break;
		case "martes":
			echo "Hoy toca pecho y tríceps";
			break;
		case "miércoles":
			echo "Hoy toca cardio y abdominales";
			break;
		case "jueves":
			echo "Hoy toca espalda y bíceps";
			break;
		case "viernes":
			echo "Hoy toca hombro";
			break;
		case "sábado":
			echo "Hoy toca clase colectiva";
			break;
		case "domingo":
			echo "Hoy toca descanso";
			break;
		default:
			echo "Ese día no existe";
	}
?>
