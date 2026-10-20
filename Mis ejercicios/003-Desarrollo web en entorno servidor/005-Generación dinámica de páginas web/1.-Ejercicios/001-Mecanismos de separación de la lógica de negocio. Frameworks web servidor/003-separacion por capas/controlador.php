<?php
	// El servidor ya no pinta nada: solo devuelve los socios en formato JSON
	function dameDatos(){
		$db = new SQLite3('socios.db');
		$info = $db->query("SELECT * FROM socios;");
		$json = [];
		while($fila = $info->fetchArray(SQLITE3_ASSOC)){
			$json[] = $fila;
		}
		return json_encode($json);
	}

	header('Content-Type: application/json; charset=utf-8');
	echo dameDatos();
?>
