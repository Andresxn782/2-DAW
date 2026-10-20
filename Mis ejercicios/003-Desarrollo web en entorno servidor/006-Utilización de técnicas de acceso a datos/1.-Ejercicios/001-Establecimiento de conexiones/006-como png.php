<?php
	$imagen = imagecreatefromjpeg("logo-velnora.jpeg");
	$naranja = imagecolorallocate($imagen, 245, 158, 11);
	for($x = 0; $x < 20; $x++){
		imagesetpixel($imagen, $x, 0, $naranja);
	}

	// PNG no pierde información: el color se guarda exacto
	imagepng($imagen, "resultado.png", 0);
	imagedestroy($imagen);

	$comprobar = imagecreatefrompng("resultado.png");
	$c = imagecolorat($comprobar, 5, 0);
	echo "Guardado como PNG. Al leerlo de nuevo: RGB ".(($c >> 16) & 0xFF).", ".(($c >> 8) & 0xFF).", ".($c & 0xFF);
?>
