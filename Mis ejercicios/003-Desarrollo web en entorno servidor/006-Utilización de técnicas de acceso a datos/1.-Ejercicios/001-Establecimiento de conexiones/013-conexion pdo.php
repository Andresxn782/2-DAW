<?php
	// Conexión con PDO: la misma forma de trabajar sirve para SQLite, MySQL, PostgreSQL...
	try{
		$db = new PDO('sqlite:socios.db');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		echo "Conectado con PDO (controlador ".$db->getAttribute(PDO::ATTR_DRIVER_NAME).")<br>";
		echo "Socios: ".$db->query("SELECT COUNT(*) FROM socios")->fetchColumn()."<br>";
		echo "Controladores PDO disponibles: ".implode(", ", PDO::getAvailableDrivers())."<br>";

		// Provoco un error a propósito para ver que se captura
		$db->query("SELECT * FROM tabla_que_no_existe");
	}catch(PDOException $e){
		echo "Error capturado: ".htmlspecialchars($e->getMessage());
	}
	$db = null;		// Cierro la conexión
?>
