<?php
	// Pequeña utilidad para crear un usuario con la contraseña cifrada.
	// Uso: php crear_usuario.php usuario contraseña "Nombre" rol
	if(php_sapi_name() !== 'cli'){
		die("Solo desde la terminal");
	}
	$db = new SQLite3('gimnasio.db');
	$consulta = $db->prepare("INSERT INTO usuarios (usuario, contrasena, nombre, rol) VALUES (:u, :c, :n, :r)");
	$consulta->bindValue(':u', $argv[1]);
	$consulta->bindValue(':c', password_hash($argv[2], PASSWORD_DEFAULT));
	$consulta->bindValue(':n', $argv[3]);
	$consulta->bindValue(':r', $argv[4]);
	$consulta->execute();
	echo "Usuario creado\n";
?>
