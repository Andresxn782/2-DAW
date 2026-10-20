<?php
	// Lo contrario de 010: leo los píxeles y reconstruyo el texto.
	// Paro en el primer canal que valga 0, porque la imagen de partida era negra.
	$imagen = imagecreatefrompng("resultadoloco.png");
	$anchura = imagesx($imagen);
	$altura = imagesy($imagen);

	$texto = "";
	for($y = 0; $y < $altura; $y++){
		for($x = 0; $x < $anchura; $x++){
			$c = imagecolorat($imagen, $x, $y);
			foreach([($c >> 16) & 0xFF, ($c >> 8) & 0xFF, $c & 0xFF] as $byte){
				if($byte == 0){
					break 3;		// Salgo de los tres bucles
				}
				$texto .= chr($byte);
			}
		}
	}
	imagedestroy($imagen);

	echo "<pre>".htmlspecialchars($texto)."</pre>";
?>
