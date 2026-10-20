<?php
	/*
		Paso los datos de gimnasio.db (SQLite) a archivos CSV en datos/gimnasio/.
		Se ejecuta una vez, desde el navegador o con: php importar_ejemplo.php
		Las contraseñas ya están cifradas con password_hash, así que se copian tal cual.
	*/
	require_once __DIR__."/config.php";
	require_once __DIR__."/Clases/VelnoraCSV.php";

	$sqlite = new PDO('sqlite:'.__DIR__.'/gimnasio.db');
	$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$csv = new VelnoraCSV($config['ruta_datos']);
	$csv->crearBaseDatos($config['basededatos']);

	$tablas = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'")->fetchAll(PDO::FETCH_COLUMN);
	foreach($tablas as $tabla){
		$columnas = array_column($sqlite->query("PRAGMA table_info(".$tabla.")")->fetchAll(PDO::FETCH_ASSOC), 'name');
		$csv->borrarTabla($config['basededatos'], $tabla);
		$csv->crearTabla($config['basededatos'], $tabla, $columnas);
		$filas = 0;
		foreach($sqlite->query("SELECT * FROM ".$tabla, PDO::FETCH_ASSOC) as $fila){
			$csv->insertar($config['basededatos'], $tabla, $fila);
			$filas++;
		}
		echo $tabla.": ".$filas." filas".(php_sapi_name() === 'cli' ? "\n" : "<br>");
	}
	echo php_sapi_name() === 'cli' ? "Importación terminada\n" : "<p>Importación terminada. <a href='index.php'>Entrar en Pulso</a></p>";
?>
