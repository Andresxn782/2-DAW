<?php
	session_start();
	if($_SERVER['REQUEST_METHOD'] !== 'POST'){
		header("Location:index.php");
		exit();
	}

	$db = new SQLite3('gimnasio.db');
	// Busco al usuario solo por su nombre; la contraseña la compruebo después con su hash
	$consulta = $db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
	$consulta->bindValue(':usuario', $_POST['usuario']);
	$result = $consulta->execute();
	$fila = $result->fetchArray(SQLITE3_ASSOC);

	if($fila && password_verify($_POST['contrasena'], $fila['contrasena'])){
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
