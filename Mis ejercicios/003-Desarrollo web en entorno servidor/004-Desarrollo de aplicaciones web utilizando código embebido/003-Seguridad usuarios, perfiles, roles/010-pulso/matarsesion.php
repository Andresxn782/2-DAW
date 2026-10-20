<?php
	session_start();
	$_SESSION = [];						// Vacío los datos de la sesión
	session_destroy();					// Y la destruyo
	setcookie(session_name(), "", time() - 3600, "/");	// Borro la cookie de sesión
	header("Location:index.php");
	exit();
?>
