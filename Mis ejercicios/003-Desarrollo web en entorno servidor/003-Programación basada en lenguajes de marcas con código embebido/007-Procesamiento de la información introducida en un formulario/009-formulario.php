<?php
	// Con las columnas genero un campo de formulario por cada una
	$db = new SQLite3('gimnasio.db');
	$result = $db->query("PRAGMA table_info(socios)");
	while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
		echo "<input type='text' name='".$column['name']."' placeholder='".$column['name']."'><br>";
	}
	$db->close();
?>
