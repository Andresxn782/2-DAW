<?php
	// Guardo cada letra del texto como el canal rojo de un píxel de la primera fila
	$texto = "Velnora";
	$imagen = imagecreatefrompng("negra.png");

	for($i = 0; $i < strlen($texto); $i++){
		$color = imagecolorallocate($imagen, ord($texto[$i]), 0, 0);
		imagesetpixel($imagen, $i, 0, $color);
	}
	imagepng($imagen, "resultadoloco.png", 0);
	imagedestroy($imagen);

	echo "Texto guardado en ".strlen($texto)." píxeles";
?>
