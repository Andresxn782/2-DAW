<?php
	session_start();
	session_destroy();					// Borro la sesión
	setcookie("llave", "", time() - 3600);	// Y caduco la cookie poniéndole una fecha pasada
	header("Location:index.php");
?>
