<?php
	$gimnasio = "Velnora";

	// Cada letra es un número (su código ASCII): ord() lo da y chr() lo devuelve a letra
	for($i = 0; $i < strlen($gimnasio); $i++){
		$codigo = ord($gimnasio[$i]);
		echo $gimnasio[$i]." = ".$codigo." → ".chr($codigo)."<br>";
	}
?>
