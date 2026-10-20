<?php
	// Funciones sueltas: cada una abre su propia conexión
	function dameDatos($tabla){
		$db = new PDO('sqlite:socios.db');
		$info = $db->query("SELECT * FROM ".$tabla);
		return $info->fetchAll(PDO::FETCH_ASSOC);
	}

	function insertaDatos($tabla, $datos){
		$db = new PDO('sqlite:socios.db');
		$db->query("INSERT INTO ".$tabla." VALUES(".$datos.")");
		return 0;
	}

	function eliminaDatos($tabla, $id){
		$db = new PDO('sqlite:socios.db');
		$db->query("DELETE FROM ".$tabla." WHERE id = ".$id);
		return 0;
	}

	echo count(dameDatos("socios"))." socios";
?>
