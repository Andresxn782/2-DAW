<?php
	// Ahora la función sirve para cualquier tabla que esté en mi lista
	function dameDatos($tabla){
		$permitidas = ["socios", "material"];
		if(!in_array($tabla, $permitidas)){
			return json_encode([]);
		}
		$db = new SQLite3('socios.db');
		$info = $db->query("SELECT * FROM ".$tabla);
		$json = [];
		while($fila = $info->fetchArray(SQLITE3_ASSOC)){
			$json[] = $fila;
		}
		return json_encode($json);
	}
?>
