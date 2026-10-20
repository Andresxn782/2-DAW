<?php
	// Pregunto a la base de datos qué columnas tiene la tabla socios
	$db = new SQLite3('gimnasio.db');
	$result = $db->query("PRAGMA table_info(socios)");
	while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
		echo $column['name']." (".$column['type'].")<br>";
	}
	$db->close();
?>
