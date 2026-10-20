<?php
	// Pruebas automáticas de la API de socios del apartado 003.
	// Se ejecuta en la consola:  php "003-probar api.php"
	// Arranca su propio servidor de PHP, lanza las peticiones y comprueba
	// el código de estado y el contenido de cada respuesta.

	$carpetaApi = realpath(__DIR__."/../003-Generación de un servicio web/003-API");
	$puerto = 8790;
	$base = "http://127.0.0.1:".$puerto."/SoftwareA/api.php";
	$clave = "velnora_2026_clave_segura";

	// La salida del servidor la tiro a la basura (NUL en Windows, /dev/null en Linux)
	$basura = DIRECTORY_SEPARATOR === "\\" ? "NUL" : "/dev/null";
	$servidor = proc_open(
		[PHP_BINARY, "-S", "127.0.0.1:".$puerto, "-t", $carpetaApi],
		[1 => ["file", $basura, "w"], 2 => ["file", $basura, "w"]],
		$tuberias
	);
	if ($servidor === false) {
		echo "No he podido arrancar el servidor de pruebas\n";
		exit(1);
	}
	usleep(500000);

	function peticion($url, $clave = null) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		if ($clave !== null) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-API-Key: ".$clave]);
		}
		$cuerpo = curl_exec($curl);
		$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$tipo = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
		curl_close($curl);
		return ["codigo" => $codigo, "tipo" => (string)$tipo, "json" => json_decode((string)$cuerpo, true)];
	}

	$aciertos = 0;
	$fallos = 0;
	function comprobar($descripcion, $condicion) {
		global $aciertos, $fallos;
		if ($condicion) {
			$aciertos++;
			echo "  OK    ".$descripcion."\n";
		} else {
			$fallos++;
			echo "  FALLO ".$descripcion."\n";
		}
	}

	$r = peticion($base);
	comprobar("Sin clave devuelve 401", $r['codigo'] === 401);
	comprobar("El error viene en JSON", ($r['json']['error'] ?? false) === true);

	$r = peticion($base, "otra_clave");
	comprobar("Con clave falsa devuelve 401", $r['codigo'] === 401);

	$r = peticion($base, $clave);
	comprobar("Con clave buena devuelve 200", $r['codigo'] === 200);
	comprobar("El tipo de contenido es JSON", str_starts_with($r['tipo'], "application/json"));
	comprobar("Devuelve 15 socios", ($r['json']['total'] ?? 0) === 15 && count($r['json']['socios']) === 15);

	$r = peticion($base."?plan=Yoga", $clave);
	$soloYoga = true;
	foreach ($r['json']['socios'] ?? [] as $socio) {
		if ($socio['plan'] !== "Yoga") {
			$soloYoga = false;
		}
	}
	comprobar("El filtro por plan solo trae Yoga", $r['codigo'] === 200 && $soloYoga && $r['json']['total'] > 0);

	$r = peticion($base."?id=1", $clave);
	comprobar("El socio 1 es Lucía", ($r['json']['nombre'] ?? "") === "Lucía");

	$r = peticion($base."?id=999", $clave);
	comprobar("Un socio que no existe devuelve 404", $r['codigo'] === 404);

	proc_terminate($servidor);
	proc_close($servidor);

	echo "\n".$aciertos." correctas, ".$fallos." fallidas\n";
	exit($fallos === 0 ? 0 : 1);
?>
