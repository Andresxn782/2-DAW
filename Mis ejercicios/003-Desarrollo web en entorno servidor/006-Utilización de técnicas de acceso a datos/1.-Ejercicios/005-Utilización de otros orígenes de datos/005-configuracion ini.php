<?php
	// Leo la configuración de un archivo .ini en lugar de escribirla en el código.
	// true = respetar las secciones; INI_SCANNER_TYPED = convertir true/false y números.
	$config = parse_ini_file("configuracion.ini", true, INI_SCANNER_TYPED);

	$bd = $config['basedatos'];
	// Una variable de entorno, si existe, tiene preferencia (así se hace en servidores reales)
	$bd['contrasena'] = getenv('VELNORA_BD_CONTRASENA') ?: $bd['contrasena'];

	$dsn = $bd['tipo'].":host=".$bd['servidor'].";port=".$bd['puerto'].";dbname=".$bd['nombre'].";charset=utf8mb4";
	echo "<p>Aplicación: ".htmlspecialchars($config['aplicacion']['nombre'])."</p>";
	echo "<p>DSN que se usaría: <code>".htmlspecialchars($dsn)."</code></p>";
	echo "<p>Depuración: ".var_export($config['aplicacion']['depuracion'], true)." (tipo ".gettype($config['aplicacion']['depuracion']).")</p>";
	echo "<p>Socios por página: ".$config['aplicacion']['socios_por_pagina']." (tipo ".gettype($config['aplicacion']['socios_por_pagina']).")</p>";

	try{
		$db = new PDO($dsn, $bd['usuario'], $bd['contrasena'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		echo "<p>Conexión correcta: ".$db->query("SELECT COUNT(*) FROM socios")->fetchColumn()." socios en MySQL.</p>";
	}catch(PDOException $e){
		echo "<p>MySQL no está disponible (arranca MySQL en XAMPP y ejecuta antes 001-Establecimiento de conexiones/014-conexion mysql.php).</p>";
	}
?>
