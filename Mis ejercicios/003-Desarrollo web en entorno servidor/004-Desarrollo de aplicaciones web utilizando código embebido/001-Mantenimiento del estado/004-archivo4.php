<?php
	session_start();
	// La sesión sí conserva los datos entre páginas
	echo "Socio: ".$_SESSION['socio']."<br>";
	echo "Plan: ".$_SESSION['plan'];
?>
