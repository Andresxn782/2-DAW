<?php
	// Software A: la API más sencilla posible.
	// Escribo el JSON a mano, como texto, para ver que no hay magia.
	// Aviso al cliente de que lo que le llega es JSON y no HTML.
	header("Content-Type: application/json; charset=UTF-8");
	echo '
	{
		"nombre": "Andres",
		"apellidos": "Ruiz",
		"email": "andres.ruiz@velnora.es",
		"plan": "Musculación"
	}
	';
?>
