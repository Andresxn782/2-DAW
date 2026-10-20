<?php
	// Datos: leo los socios y los devuelvo en JSON
	function dameDatos(){
		$db = new SQLite3('socios.db');
		$info = $db->query("SELECT * FROM socios;");
		$json = [];
		while($fila = $info->fetchArray(SQLITE3_ASSOC)){
			$json[] = $fila;
		}
		return json_encode($json);
	}
?>
