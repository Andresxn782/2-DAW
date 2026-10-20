<?php
	session_start();
	if(
		$_POST['usuario'] == "velnora"		// Si el usuario es velnora
		&&
		$_POST['contrasena'] == "velnora"	// Y la contraseña es velnora
	){
		$_SESSION['llave'] = "velnora";		// Le doy la llave
		setcookie("llave", "velnora", time() + 60*60*24);	// Y la recuerdo un día en una cookie
		header("Location:index.php");
	}else{
		header("Location:index.php?error=1");
	}
?>
