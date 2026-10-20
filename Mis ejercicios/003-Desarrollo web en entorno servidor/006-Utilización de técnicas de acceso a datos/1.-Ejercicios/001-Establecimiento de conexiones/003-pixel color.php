<?php
	// Abro la imagen como si fuera un almacén de datos y leo un píxel
	$imagen = imagecreatefromjpeg("logo-velnora.jpeg");
	$color = imagecolorat($imagen, 0, 0);
	imagedestroy($imagen);
	echo "El píxel (0,0) vale ".$color;
?>
