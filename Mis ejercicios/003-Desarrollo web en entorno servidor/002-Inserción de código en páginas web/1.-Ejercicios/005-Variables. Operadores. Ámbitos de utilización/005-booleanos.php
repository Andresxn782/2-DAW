<?php
	$tieneCuota = true;
	$tieneReserva = false;
	$esAdmin = false;

	// && solo es verdadero si todo es verdadero
	var_dump($tieneCuota && $tieneReserva); echo "<br>";
	// || es verdadero si al menos uno lo es
	var_dump($tieneCuota || $tieneReserva); echo "<br>";
	// ! le da la vuelta
	var_dump(!$esAdmin); echo "<br>";

	if($tieneCuota && !$tieneReserva){
		echo "Puedes reservar una clase";
	}
?>
