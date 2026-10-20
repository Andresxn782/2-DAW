<?php
	// Pruebas de la unidad 9.  Uso:  php 003-pruebas.php
	$base = dirname(__DIR__);
	require $base."/005-Utilización de librerías de código relacionadas con Big Data e inteligencia de negocios/AnalizadorAccesos.php";

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

	echo "Analizador de accesos (con un CSV pequeño que conozco)\n";
	$cache = sys_get_temp_dir()."/velnora-cache-pruebas-".getmypid();
	$analizador = new AnalizadorAccesos(__DIR__."/datos de prueba/accesos-mini.csv", $cache);
	$filas = iterator_to_array($analizador->filas(), false);
	comprobar("El generador salta la fila rota: 6 filas", count($filas) === 6);
	comprobar("Cada fila es un array con los nombres de la cabecera", $filas[0]['plan'] === "Musculación" && $filas[0]['minutos'] === "60");
	$d = $analizador->indicadores();
	comprobar("6 visitas de 4 socios distintos", $d['total_visitas'] === 6 && $d['socios_distintos'] === 4);
	comprobar("Duración media 60 minutos", $d['minutos_medios'] == 60);
	comprobar("3 días con visitas, récord el 5 de enero con 3", $d['dias'] === 3 && $d['dia_record']['fecha'] === "2026-01-05" && $d['dia_record']['visitas'] === 3);
	comprobar("Hora punta: lunes a las 19", $d['hora_punta']['dia'] === "Lunes" && $d['hora_punta']['hora'] === 19);
	comprobar("Enero: 4 visitas de 3 socios", $d['meses'][0] === ["mes" => "2026-01", "visitas" => 4, "socios_activos" => 3]);
	comprobar("Musculación es el plan más usado", $d['planes'][0]['plan'] === "Musculación" && $d['planes'][0]['visitas'] === 3);
	comprobar("El mapa de calor tiene 7 días de 17 horas", count($d['mapa_calor']) === 7 && count($d['mapa_calor'][0]['valores']) === 17);
	comprobar("La primera vez se calcula", $d['desde_cache'] === false);
	comprobar("La segunda vez sale de la caché", $analizador->indicadores()['desde_cache'] === true);
	array_map("unlink", glob($cache."/*"));
	rmdir($cache);

	$vacio = tempnam(sys_get_temp_dir(), "velnora");
	file_put_contents($vacio, "fecha;hora;minutos;socio;plan\n");
	try {
		(new AnalizadorAccesos($vacio))->calcular();
		comprobar("Un CSV vacío lanza una excepción", false);
	} catch (RuntimeException $e) {
		comprobar("Un CSV vacío lanza una excepción", true);
	}
	unlink($vacio);

	echo "Catálogo de Velnora TV y comunidad (por HTTP)\n";
	$puerto = 8793;
	$basura = DIRECTORY_SEPARATOR === "\\" ? "NUL" : "/dev/null";
	$servidor = proc_open([PHP_BINARY, "-S", "127.0.0.1:".$puerto, "-t", $base],
		[1 => ["file", $basura, "w"], 2 => ["file", $basura, "w"]], $tuberias);
	usleep(600000);
	function pedir($ruta) {
		global $puerto;
		// Codifico los espacios y acentos de la ruta, pero no la parte de después de "?"
		$trozos = explode("?", $ruta, 2);
		$url = "http://127.0.0.1:".$puerto."/".implode("/", array_map("rawurlencode", explode("/", $trozos[0])));
		if (isset($trozos[1])) {
			$url .= "?".$trozos[1];
		}
		$curl = curl_init($url);
		curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
		$texto = (string)curl_exec($curl);
		$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$tipo = (string)curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
		curl_close($curl);
		return ["codigo" => $codigo, "tipo" => $tipo, "texto" => $texto, "json" => json_decode($texto, true)];
	}
	$catalogo = "003-Utilización de información proveniente de repositorios/004-scroll infinito y filtros/servidor.php";
	$r = pedir($catalogo);
	comprobar("El catálogo tiene 60 vídeos y devuelve 12", $r['json']['total'] === 60 && count($r['json']['entrenos']) === 12 && $r['json']['hayMas'] === true);
	$r = pedir($catalogo."?pagina=4");
	comprobar("La última página tiene 12 y ya no hay más", count($r['json']['entrenos']) === 12 && $r['json']['hayMas'] === false);
	$r = pedir($catalogo."?categoria=Yoga&nivel=".urlencode("Iniciación"));
	$bien = true;
	foreach ($r['json']['entrenos'] as $video) {
		$bien = $bien && $video['categoria'] === "Yoga" && $video['nivel'] === "Iniciación";
	}
	comprobar("Los filtros de categoría y nivel se cumplen", $bien && $r['json']['total'] > 0);
	$r = pedir($catalogo."?q=MARTA");
	comprobar("La búsqueda no distingue mayúsculas y busca por monitor", $r['json']['total'] > 0 && str_contains($r['json']['entrenos'][0]['monitor'], "Marta"));
	$r = pedir("003-Utilización de información proveniente de repositorios/repositorio/miniatura.php?id=1");
	comprobar("La miniatura es un SVG", $r['codigo'] === 200 && str_starts_with($r['tipo'], "image/svg+xml") && str_contains($r['texto'], "<svg"));
	$r = pedir("003-Utilización de información proveniente de repositorios/repositorio/miniatura.php?id=999");
	comprobar("Miniatura de un vídeo que no existe: 404", $r['codigo'] === 404);
	$r = pedir("004-Incorporación de funcionalidades específicas/006-datos actividad.php");
	comprobar("Datos de actividad: 30 días y 3 tipos de reacción", count($r['json']['publicaciones']['valores']) === 30 && count($r['json']['reacciones']['valores']) === 3);
	$r = pedir("004-Incorporación de funcionalidades específicas/005-reaccionar.php");
	comprobar("Reaccionar sin token ni POST: 403", $r['codigo'] === 403);

	proc_terminate($servidor);
	proc_close($servidor);
	echo "\n".$aciertos." correctas, ".$fallos." fallidas\n";
	exit($fallos === 0 ? 0 : 1);
?>
