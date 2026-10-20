<?php
	// Pruebas de Velnora Hub.  Uso:  php pruebas.php
	// Trabajan en una carpeta temporal para no tocar los datos ni la caché de verdad.
	require __DIR__."/inicio.php";

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

	$temporal = sys_get_temp_dir()."/velnora-hub-".getmypid();
	mkdir($temporal."/datos", 0777, true);
	foreach (["rutas.geojson", "entrenos.json", "prediccion-ejemplo.xml"] as $archivo) {
		copy(__DIR__."/datos/".$archivo, $temporal."/datos/".$archivo);
	}
	// Un martes a las 11:30 para que los resultados no dependan de cuándo se ejecute
	$martes = strtotime("2026-10-20 11:30:00");

	echo "Clases (SQLite)\n";
	$clases = (new FuenteClases($temporal."/datos", $martes))->obtener();
	comprobar("Hoy es martes y mañana miércoles", $clases['hoy']['dia'] === "Martes" && $clases['manana']['dia'] === "Miércoles");
	comprobar("De hoy solo salen las que no han empezado", count($clases['hoy']['clases']) > 0 && min(array_column($clases['hoy']['clases'], "hora")) >= "11:30");
	comprobar("Mañana tiene 4 clases ordenadas por hora", count($clases['manana']['clases']) === 4 && array_column($clases['manana']['clases'], "hora") === array_values(array_unique(array_merge(...[array_column($clases['manana']['clases'], "hora")]))));

	echo "Tiempo (XML de la AEMET)\n";
	$tiempo = FuenteTiempo::interpretar(simplexml_load_file($temporal."/datos/prediccion-ejemplo.xml"), "prueba");
	comprobar("Saca 3 días de Madrid", count($tiempo['dias']) === 3 && $tiempo['municipio'] === "Madrid");
	comprobar("El primer día tiene 80% de lluvia: no se entrena fuera", $tiempo['dias'][0]['lluvia'] === 80 && $tiempo['dias'][0]['entrenar_fuera'] === false);
	comprobar("Lee las temperaturas", $tiempo['dias'][0]['maxima'] === 27 && $tiempo['dias'][0]['minima'] === 18);
	$sinRed = (new FuenteTiempo(["url" => "http://127.0.0.1:9/no-existe.xml", "timeout" => 2], $temporal."/datos"))->obtener();
	comprobar("Si la AEMET no responde, usa la copia y lo dice", str_contains($sinRed['origen'], "Copia de ejemplo") && count($sinRed['dias']) === 3);

	echo "Rutas (GeoJSON)\n";
	comprobar("Un grado de latitud son unos 111 km", abs(FuenteRutas::distancia([0, 0], [0, 1]) - 111.19) < 0.1);
	$rutas = (new FuenteRutas($temporal."/datos"))->obtener()['rutas'];
	comprobar("Hay 3 rutas", count($rutas) === 3);
	comprobar("La vuelta al Retiro es circular y mide entre 3 y 5 km", $rutas[0]['circular'] && $rutas[0]['km'] > 3 && $rutas[0]['km'] < 5);
	comprobar("Madrid Río no es circular", $rutas[1]['circular'] === false);
	$puntos = array_map(function ($p) { return array_map("floatval", explode(",", $p)); }, explode(" ", $rutas[0]['trazado']));
	$dentro = true;
	foreach ($puntos as [$x, $y]) {
		$dentro = $dentro && $x >= 0 && $x <= 200 && $y >= 0 && $y <= 140;
	}
	comprobar("El trazado SVG cabe en el dibujo de 200 x 140", $dentro && count($puntos) === 12);

	echo "Afluencia (CSV masivo)\n";
	$afluencia = (new FuenteAfluencia($temporal."/datos", $martes))->obtener();
	comprobar("Genera y lee el CSV de 28 días", file_exists($temporal."/datos/accesos.csv") && $afluencia['registros_leidos'] > 15000);
	comprobar("Usa los 4 martes", $afluencia['dia'] === "Martes" && $afluencia['semanas'] === 4);
	comprobar("17 horas, de 6 a 22", $afluencia['horas'] === range(6, 22));
	comprobar("La hora más llena es por la tarde", $afluencia['horas'][array_search(max($afluencia['medias']), $afluencia['medias'])] >= 17);
	comprobar("A las 11 está tranquilo y la mejor hora que queda es antes de las 17", $afluencia['ahora'] === "tranquilo" && $afluencia['mejor_hora_restante'] > 11 && $afluencia['mejor_hora_restante'] < 17);
	$deNoche = (new FuenteAfluencia($temporal."/datos", strtotime("2026-10-20 03:00:00")))->obtener();
	comprobar("A las 3 de la mañana está cerrado", $deNoche['ahora'] === "cerrado");

	echo "Vídeos (repositorio JSON)\n";
	$lluvia = (new FuenteVideos($temporal."/datos", false))->obtener();
	$categorias = array_unique(array_column($lluvia['videos'], "categoria"));
	comprobar("Con mal tiempo recomienda 3 vídeos de interior de 30 minutos o menos", count($lluvia['videos']) === 3 && !array_diff($categorias, ["Fuerza", "Cardio", "Crossfit"]) && max(array_column($lluvia['videos'], "minutos")) <= 30);
	$sol = (new FuenteVideos($temporal."/datos", true))->obtener();
	comprobar("Con buen tiempo, movilidad o yoga", !array_diff(array_column($sol['videos'], "categoria"), ["Movilidad", "Yoga"]));

	echo "Caché\n";
	$cacheDePrueba = new Cache($temporal."/cache");
	$llamadas = 0;
	$calcular = function () use (&$llamadas) {
		$llamadas++;
		return ["valor" => 42];
	};
	$primera = $cacheDePrueba->recordar("prueba", 60, $calcular);
	$segunda = $cacheDePrueba->recordar("prueba", 60, $calcular);
	comprobar("La segunda vez no se recalcula", $llamadas === 1 && $primera['desde_cache'] === false && $segunda['desde_cache'] === true && $segunda['datos']['valor'] === 42);
	touch($temporal."/cache/prueba.json", time() - 120);
	$cacheDePrueba->recordar("prueba", 60, $calcular);
	comprobar("Cuando caduca, se recalcula", $llamadas === 2);
	comprobar("vaciar() borra los archivos", $cacheDePrueba->vaciar() === 1);

	echo "API por HTTP\n";
	$puerto = 8794;
	$basura = DIRECTORY_SEPARATOR === "\\" ? "NUL" : "/dev/null";
	$servidor = proc_open([PHP_BINARY, "-S", "127.0.0.1:".$puerto, "-t", __DIR__], [1 => ["file", $basura, "w"], 2 => ["file", $basura, "w"]], $tuberias);
	usleep(600000);
	function api($ruta) {
		global $puerto;
		$curl = curl_init("http://127.0.0.1:".$puerto."/".$ruta);
		curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20]);
		$texto = (string)curl_exec($curl);
		$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		return ["codigo" => $codigo, "json" => json_decode($texto, true), "texto" => $texto];
	}
	foreach (["clases", "tiempo", "rutas", "afluencia", "videos"] as $fuente) {
		$r = api("api.php?fuente=".$fuente);
		comprobar("api.php?fuente=".$fuente." responde 200 con datos", $r['codigo'] === 200 && $r['json']['fuente'] === $fuente && is_array($r['json']['datos']));
	}
	$r = api("api.php?fuente=tiempo");
	comprobar("La segunda petición del tiempo sale de la caché", $r['json']['desde_cache'] === true);
	$r = api("api.php?fuente=bolsa");
	comprobar("Fuente desconocida: 404", $r['codigo'] === 404);
	$r = api("index.php");
	comprobar("La portada sale con las clases ya pintadas por el servidor", $r['codigo'] === 200 && str_contains($r['texto'], "Clases de hoy") && str_contains($r['texto'], "<li"));
	$r = api("miniatura.php?id=3");
	comprobar("Las carátulas se generan", str_contains($r['texto'], "<svg"));
	proc_terminate($servidor);
	proc_close($servidor);

	// Limpio la carpeta temporal
	foreach (["datos/*", "cache/*"] as $patron) {
		array_map("unlink", glob($temporal."/".$patron));
	}
	rmdir($temporal."/datos");
	@rmdir($temporal."/cache");
	rmdir($temporal);

	echo "\n".$aciertos." correctas, ".$fallos." fallidas\n";
	exit($fallos === 0 ? 0 : 1);
?>
