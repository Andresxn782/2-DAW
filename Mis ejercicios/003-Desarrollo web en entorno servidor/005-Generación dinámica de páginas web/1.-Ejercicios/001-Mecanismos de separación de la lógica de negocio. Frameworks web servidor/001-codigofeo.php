<?php
	// Funciona, pero conexión, consulta y salida están mezcladas en cuatro líneas
	$db=new SQLite3('socios.db');
	$info = $db->query("SELECT * FROM socios;");
	while($fila = $info->fetchArray(SQLITE3_ASSOC)){
		echo $fila['nombre']." ";
	}
?>
