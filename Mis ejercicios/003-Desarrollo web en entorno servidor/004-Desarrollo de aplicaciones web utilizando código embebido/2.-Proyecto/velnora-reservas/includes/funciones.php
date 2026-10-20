<?php
	// Devuelve el usuario conectado o null si no hay sesión
	function usuarioActual(){
		return $_SESSION['usuario'] ?? null;
	}

	// Si no hay sesión, mando al login
	function exigirLogin(){
		if(usuarioActual() === null){
			header("Location: index.php");
			exit();
		}
	}

	// Si el rol no es el pedido, no dejo pasar
	function exigirRol($rol){
		exigirLogin();
		if($_SESSION['usuario']['rol'] !== $rol){
			http_response_code(403);
			die("No tienes permiso para ver esta página.");
		}
	}

	// Comprueba usuario y contraseña. Devuelve el usuario sin la contraseña, o false
	function comprobarCredenciales($db, $usuario, $contrasena){
		$consulta = $db->prepare("SELECT * FROM usuarios WHERE usuario = :u");
		$consulta->bindValue(':u', $usuario);
		$fila = $consulta->execute()->fetchArray(SQLITE3_ASSOC);
		if($fila && password_verify($contrasena, $fila['contrasena'])){
			unset($fila['contrasena']);
			return $fila;
		}
		return false;
	}

	// Plazas que quedan en una clase
	function plazasLibres($db, $clase){
		$consulta = $db->prepare("SELECT plazas - (SELECT COUNT(*) FROM reservas WHERE clase = :id) FROM clases WHERE id = :id");
		$consulta->bindValue(':id', $clase);
		return (int)$consulta->execute()->fetchArray(SQLITE3_NUM)[0];
	}

	// Token contra envíos de formularios desde otras webs (CSRF)
	function token(){
		if(!isset($_SESSION['token'])){
			$_SESSION['token'] = bin2hex(random_bytes(16));
		}
		return $_SESSION['token'];
	}
	function tokenValido(){
		return isset($_POST['token']) && hash_equals(token(), $_POST['token']);
	}

	// Tema elegido por el usuario, guardado en una cookie del navegador
	function tema(){
		return (($_COOKIE['tema'] ?? 'claro') === 'oscuro') ? 'oscuro' : 'claro';
	}

	function e($texto){
		return htmlspecialchars((string)$texto);
	}
?>
