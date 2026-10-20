<?php
	// Ahora uso los tres canales (rojo, verde y azul): caben 3 letras por píxel.
	// El texto es el reglamento del gimnasio.
	$texto = "REGLAMENTO DEL GIMNASIO VELNORA
1. Usa siempre toalla en las máquinas y límpialas al terminar.
2. Devuelve las mancuernas y los discos a su sitio.
3. Las clases dirigidas se reservan desde la web; si no vas, cancela la reserva.
4. Respeta los turnos en las máquinas de cardio: máximo 30 minutos si hay gente esperando.
5. No se puede entrar a la sala con calzado de calle.
6. Si notas dolor o mareo, para y avisa a un monitor.
7. Las taquillas se vacían cada noche.
Horario: de lunes a viernes de 7:00 a 23:00; sábados de 9:00 a 20:00; domingos de 9:00 a 14:00.";

	$imagen = imagecreatefrompng("negra.png");
	$anchura = imagesx($imagen);
	$altura = imagesy($imagen);

	$x = 0;
	$y = 0;
	for($i = 0; $i < strlen($texto); $i += 3){
		$r = isset($texto[$i]) ? ord($texto[$i]) : 0;
		$g = isset($texto[$i + 1]) ? ord($texto[$i + 1]) : 0;
		$b = isset($texto[$i + 2]) ? ord($texto[$i + 2]) : 0;

		$color = imagecolorallocate($imagen, $r, $g, $b);
		imagesetpixel($imagen, $x, $y, $color);

		// Siguiente píxel; al llegar al borde, siguiente fila
		$x++;
		if($x >= $anchura){
			$x = 0;
			$y++;
		}
		if($y >= $altura){
			break;
		}
	}
	imagepng($imagen, "resultadoloco.png", 0);
	imagedestroy($imagen);

	echo "Guardados ".strlen($texto)." bytes en ".ceil(strlen($texto) / 3)." píxeles de resultadoloco.png";
?>
