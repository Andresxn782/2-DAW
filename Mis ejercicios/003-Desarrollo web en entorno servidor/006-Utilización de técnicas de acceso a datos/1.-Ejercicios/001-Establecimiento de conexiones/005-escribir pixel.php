<?php
	$imagen = imagecreatefromjpeg("logo-velnora.jpeg");

	// Pinto una fila de 20 píxeles naranjas arriba a la izquierda
	$naranja = imagecolorallocate($imagen, 245, 158, 11);
	for($x = 0; $x < 20; $x++){
		imagesetpixel($imagen, $x, 0, $naranja);
	}

	imagejpeg($imagen, "resultado.jpeg");
	imagedestroy($imagen);

	// Al volver a leerla, el JPEG ha cambiado un poco el color porque comprime con pérdida
	$comprobar = imagecreatefromjpeg("resultado.jpeg");
	$c = imagecolorat($comprobar, 5, 0);
	echo "Guardado. Al leerlo de nuevo: RGB ".(($c >> 16) & 0xFF).", ".(($c >> 8) & 0xFF).", ".($c & 0xFF)." (pedí 245, 158, 11)";
?>
