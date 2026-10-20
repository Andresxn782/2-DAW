<?php
	session_start();
	if(
    $_POST['usuario'] == "velnora" 								// Si el usuario es velnora
    && 
    $_POST['contrasena'] == "velnora"							// Y la contraseña es velnora
    ){
      $_SESSION['llave'] = "velnora"; 						// En ese caso te doy la llave
      header("Location:007-escritorio.php"); 			// Vamos al escritorio
    }else{
    	header("Location:005-inicio de sesion.html");	// Volvemos al login
    }
?>