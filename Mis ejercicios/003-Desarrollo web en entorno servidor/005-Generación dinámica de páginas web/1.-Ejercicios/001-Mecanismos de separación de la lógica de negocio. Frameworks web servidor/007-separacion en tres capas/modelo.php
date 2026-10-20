<?php
	// Modelo: solo habla con la base de datos (ahora con PDO)
	function dameDatos($tabla){
		$permitidas = ["socios", "material"];
		if(!in_array($tabla, $permitidas)){
			return [];
		}
		$db = new PDO('sqlite:socios.db');
		$info = $db->query("SELECT * FROM ".$tabla);
		return $info->fetchAll(PDO::FETCH_ASSOC);
	}
?>
