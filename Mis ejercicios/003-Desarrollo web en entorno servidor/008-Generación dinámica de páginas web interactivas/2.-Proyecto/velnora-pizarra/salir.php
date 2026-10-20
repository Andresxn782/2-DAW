<?php
	require __DIR__."/includes/funciones.php";
	$_SESSION = [];
	session_destroy();
	header("Location: index.php");
?>
