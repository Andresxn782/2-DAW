<?php
	// Conexión con la extensión SQLite3 (solo sirve para SQLite)
	$db = new SQLite3('socios.db', SQLITE3_OPEN_READONLY);

	$total = $db->querySingle("SELECT COUNT(*) FROM socios");
	echo "Conectado con SQLite3. Hay ".$total." socios.<br>";
	echo "Versión de SQLite: ".SQLite3::version()['versionString'];

	$db->close();
?>
