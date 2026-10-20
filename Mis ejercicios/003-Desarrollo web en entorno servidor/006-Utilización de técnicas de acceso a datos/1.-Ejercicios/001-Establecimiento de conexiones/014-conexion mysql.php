<?php
	/*
		Conexión a MySQL/MariaDB de XAMPP con PDO.
		Antes hay que arrancar MySQL en el panel de XAMPP.
		El script crea la base de datos velnora y la tabla socios si no existen.
		Usuario root sin contraseña es la configuración por defecto de XAMPP (solo para clase).
	*/
	$servidor = "localhost";
	$usuario = "root";
	$contrasena = "";
	$basedatos = "velnora";

	try{
		// 1.-Conecto al servidor y creo la base de datos
		$db = new PDO("mysql:host=".$servidor.";charset=utf8mb4", $usuario, $contrasena, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		]);
		$db->exec("CREATE DATABASE IF NOT EXISTS ".$basedatos." CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci");
		$db->exec("USE ".$basedatos);

		// 2.-Creo la tabla
		$db->exec("CREATE TABLE IF NOT EXISTS socios (
			id INT AUTO_INCREMENT PRIMARY KEY,
			nombre VARCHAR(50) NOT NULL,
			apellidos VARCHAR(100),
			email VARCHAR(100) UNIQUE,
			plan VARCHAR(30)
		)");

		// 3.-Si está vacía, copio los socios de SQLite: así uso dos conexiones a la vez
		if($db->query("SELECT COUNT(*) FROM socios")->fetchColumn() == 0){
			$sqlite = new PDO('sqlite:socios.db');
			$insertar = $db->prepare("INSERT INTO socios (nombre, apellidos, email, plan) VALUES (:nombre, :apellidos, :email, :plan)");
			foreach($sqlite->query("SELECT nombre, apellidos, email, plan FROM socios", PDO::FETCH_ASSOC) as $socio){
				$insertar->execute($socio);
			}
		}

		echo "<p>Conectado a MySQL ".$db->getAttribute(PDO::ATTR_SERVER_VERSION)."</p>";
		echo "<ul>";
		foreach($db->query("SELECT nombre, plan FROM socios ORDER BY nombre LIMIT 5") as $fila){
			echo "<li>".htmlspecialchars($fila['nombre'])." · ".htmlspecialchars($fila['plan'])."</li>";
		}
		echo "</ul>";
	}catch(PDOException $e){
		echo "<p>No se ha podido conectar con MySQL. ¿Está arrancado en XAMPP?</p>";
		echo "<p><small>".htmlspecialchars($e->getMessage())."</small></p>";
	}
?>
