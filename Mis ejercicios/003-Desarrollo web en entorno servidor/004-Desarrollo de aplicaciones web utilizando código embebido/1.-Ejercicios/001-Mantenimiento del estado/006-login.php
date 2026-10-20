<?php
	session_start();
	if(
		$_POST['usuario'] == "velnora"			// Si el usuario es velnora
		&&
		$_POST['contrasena'] == "velnora"		// Y la contraseña es velnora
	){
		$_SESSION['llave'] = "velnora";			// Le doy la llave
		$_SESSION['usuario'] = $_POST['usuario'];
		header("Location:007-escritorio.php");	// Y va al escritorio
	}else{
		header("Location:005-inicio de sesion.html");	// Si no, vuelve al login
	}
?>
