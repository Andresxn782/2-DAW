<?php
	// Devuelvo la configuración como JSON para que la lea JavaScript
	include "config.php";
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($config, JSON_UNESCAPED_UNICODE);
?>
