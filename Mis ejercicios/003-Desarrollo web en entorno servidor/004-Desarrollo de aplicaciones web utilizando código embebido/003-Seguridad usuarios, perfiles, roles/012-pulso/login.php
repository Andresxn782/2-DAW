<?php
	session_start();
	if($_SERVER['REQUEST_METHOD'] !== 'POST'){
		header("Location:index.php");
		exit();
	}

	$db = new SQLite3('gimnasio.db');
	// Consulta preparada: así nadie puede colar SQL en el usuario o la contraseña
	$consulta = $db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND contrasena = :contrasena");
	$consulta->bindValue(':usuario', $_POST['usuario']);
	$consulta->bindValue(':contrasena', $_POST['contrasena']);
	$result = $consulta->execute();

	if($fila = $result->fetchArray(SQLITE3_ASSOC)){
		session_regenerate_id(true);			// Nuevo id de sesión al entrar
		$_SESSION['llave'] = "velnora";
		$_SESSION['nombre'] = $fila['nombre'];
		$_SESSION['rol'] = $fila['rol'];		// Guardo el rol para saber qué puede ver
		header("Location:index.php");
	}else{
		header("Location:index.php?error=1");
	}
	$db->close();
	exit();
?>
