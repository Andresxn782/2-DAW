<?php
	include "includes/config.php";
	// Vacío la sesión, borro su cookie y la destruyo
	$_SESSION = [];
	setcookie(session_name(), "", time() - 3600, "/");
	session_destroy();
	header("Location: index.php?salir=1");
?>
