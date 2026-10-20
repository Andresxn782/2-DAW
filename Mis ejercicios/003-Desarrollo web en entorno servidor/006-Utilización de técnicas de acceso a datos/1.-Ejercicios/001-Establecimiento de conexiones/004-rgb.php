<?php
	$imagen = imagecreatefromjpeg("logo-velnora.jpeg");

	// Leo dos píxeles: una esquina (fondo) y el centro (círculo)
	foreach([[0, 0], [100, 100]] as [$x, $y]){
		$color = imagecolorat($imagen, $x, $y);
		// El número guarda los tres canales: los separo moviendo bits
		$r = ($color >> 16) & 0xFF;
		$g = ($color >> 8) & 0xFF;
		$b = $color & 0xFF;
		echo "Píxel ($x,$y) → RGB: $r, $g, $b<br>";
	}
	imagedestroy($imagen);
?>
