<?php
	// Pruebas automáticas de las funciones principales.
	// Se ejecutan desde la terminal: php pruebas.php
	// Usan una base de datos temporal para no tocar reservas.db.
	if(php_sapi_name() !== 'cli'){
		die("Ejecuta las pruebas desde la terminal: php pruebas.php");
	}

	$_SESSION = [];
	include "includes/funciones.php";

	$db = new SQLite3(':memory:');
	$db->exec("CREATE TABLE usuarios (id INTEGER PRIMARY KEY, usuario TEXT, contrasena TEXT, nombre TEXT, rol TEXT)");
	$db->exec("CREATE TABLE clases (id INTEGER PRIMARY KEY, nombre TEXT, dia TEXT, hora TEXT, plazas INTEGER, monitor INTEGER)");
	$db->exec("CREATE TABLE reservas (id INTEGER PRIMARY KEY, usuario INTEGER, clase INTEGER)");
	$db->exec("INSERT INTO usuarios VALUES (1, 'prueba', '".password_hash("secreto123", PASSWORD_DEFAULT)."', 'Prueba', 'socio')");
	$db->exec("INSERT INTO clases VALUES (1, 'Yoga', 'Lunes', '10:00', 2, 1)");

	$total = 0;
	$fallos = 0;
	function comprobar($descripcion, $resultado){
		global $total, $fallos;
		$total++;
		if($resultado){
			echo "  OK    ".$descripcion."\n";
		}else{
			$fallos++;
			echo "  FALLO ".$descripcion."\n";
		}
	}

	echo "Pruebas de Velnora Reservas\n";
	comprobar("La contraseña correcta permite entrar", comprobarCredenciales($db, "prueba", "secreto123") !== false);
	comprobar("La contraseña incorrecta no permite entrar", comprobarCredenciales($db, "prueba", "otra") === false);
	comprobar("Un usuario que no existe no entra", comprobarCredenciales($db, "nadie", "secreto123") === false);
	comprobar("El usuario devuelto no lleva la contraseña", !isset(comprobarCredenciales($db, "prueba", "secreto123")['contrasena']));
	comprobar("Una inyección SQL en el usuario no entra", comprobarCredenciales($db, "' OR '1'='1", "x") === false);
	comprobar("Clase vacía: 2 plazas libres", plazasLibres($db, 1) === 2);
	$db->exec("INSERT INTO reservas (usuario, clase) VALUES (1, 1), (2, 1)");
	comprobar("Clase llena: 0 plazas libres", plazasLibres($db, 1) === 0);
	comprobar("e() escapa el HTML", e("<script>") === "&lt;script&gt;");
	comprobar("Sin sesión no hay usuario", usuarioActual() === null);
	$_POST['token'] = "falso";
	comprobar("Un token falso no es válido", tokenValido() === false);
	$_POST['token'] = token();
	comprobar("El token de la sesión es válido", tokenValido() === true);
	$_COOKIE['tema'] = "rojo";
	comprobar("Un tema desconocido vuelve a claro", tema() === "claro");

	echo "\n".($total - $fallos)." de ".$total." pruebas correctas\n";
	exit($fallos > 0 ? 1 : 0);
?>
