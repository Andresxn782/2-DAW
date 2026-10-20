<?php
	session_start();
	if(!isset($_SESSION['llave'])){		// Si no tiene la llave
		die("No tienes permiso para entrar aquí");	// Corto la ejecución
	}
?>
<h1>Escritorio de Velnora</h1>
<p>Has entrado correctamente como <?php echo $_SESSION['usuario']; ?>.</p>
