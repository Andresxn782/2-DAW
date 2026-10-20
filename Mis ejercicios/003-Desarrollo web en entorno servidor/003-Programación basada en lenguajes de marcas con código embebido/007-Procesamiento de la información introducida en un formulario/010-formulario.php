<form action="?" method="POST">
	<?php
		$db = new SQLite3('gimnasio.db');
		$result = $db->query("PRAGMA table_info(socios)");
		while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
			// El identificador lo pone la base de datos sola (AUTOINCREMENT)
			if($column['pk'] == 1){
				continue;
			}
			echo "<input type='text' name='".$column['name']."' placeholder='".$column['name']."'><br>";
		}
		$db->close();
	?>
	<input type="submit" value="Guardar socio">
</form>
