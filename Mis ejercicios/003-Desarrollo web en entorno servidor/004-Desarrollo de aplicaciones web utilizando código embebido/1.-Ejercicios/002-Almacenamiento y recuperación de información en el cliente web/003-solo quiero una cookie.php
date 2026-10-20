<?php
	// Compruebo que existe antes de usarla
	if(isset($_COOKIE['socio'])){
		echo "Hola de nuevo, ".$_COOKIE['socio'];
	}else{
		echo "Todavía no te conozco";
	}
?>
