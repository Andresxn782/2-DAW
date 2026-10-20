<?php
	$gimnasio = "Velnora";

	echo $gimnasio."<br>";
	echo "Primera letra: ".$gimnasio[0]."<br>";
	// Recorro el texto letra a letra, igual que un array
	for($i = 0; $i < strlen($gimnasio); $i++){
		echo $i." → ".$gimnasio[$i]."<br>";
	}
?>
