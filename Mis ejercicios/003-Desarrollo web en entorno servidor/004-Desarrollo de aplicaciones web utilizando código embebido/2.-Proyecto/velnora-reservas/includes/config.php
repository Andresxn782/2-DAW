<?php
	// Configuración general de la aplicación
	$depuracion = true;					// En clase lo dejo a true; en producción iría a false

	if($depuracion){
		ini_set('display_errors', '1');
		error_reporting(E_ALL);
	}else{
		ini_set('display_errors', '0');
	}
	// Los errores también se guardan en un archivo para revisarlos después
	ini_set('log_errors', '1');
	ini_set('error_log', __DIR__.'/../errores.log');

	// Cookie de sesión más segura: no accesible desde JavaScript
	session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
	session_start();

	$rutaBD = __DIR__.'/../reservas.db';
	$db = new SQLite3($rutaBD);
	$db->exec("PRAGMA foreign_keys = ON");

	// La primera vez creo las tablas y unos datos de ejemplo
	$existe = $db->querySingle("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='usuarios'");
	if(!$existe){
		$db->exec("CREATE TABLE usuarios (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			usuario TEXT UNIQUE NOT NULL,
			contrasena TEXT NOT NULL,
			nombre TEXT NOT NULL,
			rol TEXT NOT NULL
		)");
		$db->exec("CREATE TABLE clases (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			nombre TEXT NOT NULL,
			dia TEXT NOT NULL,
			hora TEXT NOT NULL,
			plazas INTEGER NOT NULL,
			monitor INTEGER REFERENCES usuarios(id)
		)");
		$db->exec("CREATE TABLE reservas (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			usuario INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
			clase INTEGER REFERENCES clases(id) ON DELETE CASCADE,
			UNIQUE(usuario, clase)
		)");

		$usuarios = [
			["marcos", "marcos123", "Marcos Velasco", "monitor"],
			["lucia", "lucia123", "Lucía Martín", "socio"],
			["carlos", "carlos123", "Carlos Pérez", "socio"]
		];
		$consulta = $db->prepare("INSERT INTO usuarios (usuario, contrasena, nombre, rol) VALUES (:u, :c, :n, :r)");
		foreach($usuarios as $u){
			$consulta->bindValue(':u', $u[0]);
			$consulta->bindValue(':c', password_hash($u[1], PASSWORD_DEFAULT));
			$consulta->bindValue(':n', $u[2]);
			$consulta->bindValue(':r', $u[3]);
			$consulta->execute();
			$consulta->reset();
		}
		$db->exec("INSERT INTO clases (nombre, dia, hora, plazas, monitor) VALUES
			('Spinning', 'Lunes', '18:00', 12, 1),
			('Yoga', 'Martes', '10:00', 8, 1),
			('Crossfit', 'Miércoles', '19:30', 10, 1),
			('Pilates', 'Jueves', '17:00', 2, 1)");
		$db->exec("INSERT INTO reservas (usuario, clase) VALUES (3, 4)");
	}
?>
