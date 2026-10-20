<?php
	session_start();
	if(
    $_POST['usuario'] == "velnora" 								// Si el usuario es velnora
    && 
    $_POST['contrasena'] == "velnora"							// Y la contraseña es velnora
    ){
      $_SESSION['llave'] = "velnora"; 						// En ese caso te doy la llave
      header("Location:index.php"); 			// Vamos al escritorio
    }else{
    	header("Location:ningunaparte.html");	// Volvemos al login
    }
?>