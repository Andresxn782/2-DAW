<?php
	// Pruebas de la pizarra. Uso:  php pruebas.php
	// 1) Funciones de validación y formato (sin servidor).
	// 2) La API por HTTP con dos usuarios (cada uno con su cookie de sesión).
	require __DIR__."/includes/valores.php";

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

	echo "Funciones\n";
	comprobar("412 segundos son 6:52", formatearValor(412, "tiempo") === "6:52");
	comprobar("65 segundos son 1:05", formatearValor(65, "tiempo") === "1:05");
	comprobar("Repeticiones con su unidad", formatearValor(120, "repeticiones") === "120 reps");
	comprobar("6:45 son 405 segundos", interpretarValor("6:45", "tiempo") === 405);
	comprobar("6:75 no es un tiempo", is_string(interpretarValor("6:75", "tiempo")));
	comprobar("0:10 es demasiado poco", is_string(interpretarValor("0:10", "tiempo")));
	comprobar("Texto en repeticiones no vale", is_string(interpretarValor("muchas", "repeticiones")));
	comprobar("0 repeticiones no vale", is_string(interpretarValor("0", "repeticiones")));
	comprobar("150 repeticiones vale", interpretarValor(" 150 ", "repeticiones") === 150);

	// ---------- Servidor ----------
	$puerto = 8792;
	$baseDatos = sys_get_temp_dir()."/velnora-pizarra-pruebas-".getmypid().".db";
	$entorno = getenv();
	$entorno["VELNORA_PIZARRA_BD"] = $baseDatos;
	$basura = DIRECTORY_SEPARATOR === "\\" ? "NUL" : "/dev/null";
	$servidor = proc_open([PHP_BINARY, "-S", "127.0.0.1:".$puerto, "-t", __DIR__],
		[1 => ["file", $basura, "w"], 2 => ["file", $basura, "w"]], $tuberias, __DIR__, $entorno);
	usleep(700000);

	// Cada usuario tiene su propio archivo de cookies
	function peticion($usuario, $metodo, $ruta, $datos = null, $token = null) {
		global $puerto;
		$curl = curl_init("http://127.0.0.1:".$puerto."/".$ruta);
		$galleta = sys_get_temp_dir()."/velnora-pizarra-cookie-".$usuario."-".getmypid();
		curl_setopt_array($curl, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_COOKIEJAR => $galleta,
			CURLOPT_COOKIEFILE => $galleta,
			CURLOPT_TIMEOUT => 5,
			CURLOPT_CUSTOMREQUEST => $metodo
		]);
		if ($datos !== null) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($datos));
		}
		if ($token !== null) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-Token: ".$token]);
		}
		$texto = (string)curl_exec($curl);
		$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		return ["codigo" => $codigo, "texto" => $texto, "json" => json_decode($texto, true)];
	}

	// Entra en la pizarra y devuelve el token que viene en la página
	function entrar($usuario, $datos) {
		peticion($usuario, "POST", "index.php", $datos + ["entrar" => 1]);
		$pagina = peticion($usuario, "GET", "index.php")['texto'];
		preg_match('/data-token="([0-9a-f]+)"/', $pagina, $partes);
		return [$partes[1] ?? "", $pagina];
	}

	echo "Entrada\n";
	$r = peticion("nerea", "GET", "api.php?accion=estado");
	comprobar("Sin entrar, la API responde 401", $r['codigo'] === 401);
	$r = peticion("nerea", "POST", "index.php", ["nombre" => "N", "rol" => "socio", "entrar" => 1]);
	comprobar("Nombre demasiado corto: se queda en la entrada con el error", str_contains($r['texto'], "entre 2 y 30 letras"));
	$r = peticion("andres", "POST", "index.php", ["nombre" => "Andres Ruiz", "rol" => "monitor", "pin" => "malo", "entrar" => 1]);
	comprobar("PIN de monitor incorrecto", str_contains($r['texto'], "PIN de monitor no es correcto"));
	[$tokenNerea, $pagina] = entrar("nerea", ["nombre" => "Nerea", "rol" => "socio"]);
	comprobar("Nerea entra y la página viene ya rellena por el servidor", $tokenNerea !== "" && str_contains($pagina, "Fran a la Velnora") && str_contains($pagina, 'data-socio="Carlos"'));
	comprobar("Su fila está marcada como suya", (bool)preg_match('/data-socio="Nerea" class="yo"/', $pagina));
	comprobar("Un socio no ve el formulario de WOD", !str_contains($pagina, 'id="formWod"'));
	[$tokenAndres, $pagina] = entrar("andres", ["nombre" => "Andres Ruiz", "rol" => "monitor", "pin" => "velnora"]);
	comprobar("El monitor sí ve el formulario de WOD", str_contains($pagina, 'id="formWod"'));

	echo "Estado y resultados\n";
	$r = peticion("nerea", "GET", "api.php?accion=estado&wod=1&animos_desde=0");
	comprobar("Estado: 3 resultados y 2 ánimos", $r['codigo'] === 200 && count($r['json']['ranking']) === 3 && count($r['json']['animos']) === 2);
	comprobar("Por tiempo: Carlos (6:05) va delante de Nerea (6:52)", $r['json']['ranking'][0]['socio'] === "Carlos" && $r['json']['ranking'][1]['texto'] === "6:52");
	comprobar("Los escalados van al final aunque sean más rápidos", $r['json']['ranking'][2]['socio'] === "Marcos" && $r['json']['ranking'][2]['escalado'] === true);
	$r = peticion("nerea", "GET", "api.php?accion=estado&wod=1&animos_desde=2");
	comprobar("Con animos_desde solo llegan los nuevos", count($r['json']['animos']) === 0 && $r['json']['wod_nuevo'] === false);
	$r = peticion("nerea", "POST", "api.php?accion=resultado", ["valor" => "5:59"]);
	comprobar("Sin token: 403", $r['codigo'] === 403);
	$r = peticion("nerea", "POST", "api.php?accion=resultado", ["valor" => "5:99"], $tokenNerea);
	comprobar("Tiempo mal escrito: 422 con el campo", $r['codigo'] === 422 && $r['json']['campo'] === "valor");
	$r = peticion("nerea", "POST", "api.php?accion=resultado", ["valor" => "5:59"], $tokenNerea);
	comprobar("Nerea mejora a 5:59 y pasa a ser 1ª", $r['codigo'] === 200 && $r['json']['posicion'] === 1);
	$r = peticion("nerea", "GET", "api.php?accion=estado&wod=1");
	comprobar("Sigue habiendo 3 resultados (se sustituye, no se duplica)", count($r['json']['ranking']) === 3);
	$r = peticion("nerea", "GET", "api.php?accion=resultado");
	comprobar("Guardar por GET: 405", $r['codigo'] === 405);
	$r = peticion("nerea", "POST", "api.php?accion=animo", ["texto" => str_repeat("a", 141)], $tokenNerea);
	comprobar("Ánimo demasiado largo: 422", $r['codigo'] === 422);
	$r = peticion("nerea", "POST", "api.php?accion=animo", ["texto" => "<b>¡Vamos!</b>"], $tokenNerea);
	comprobar("Ánimo guardado: 201", $r['codigo'] === 201);
	$r = peticion("andres", "GET", "api.php?accion=estado&wod=1&animos_desde=2");
	comprobar("El monitor recibe el ánimo nuevo tal cual (se escapa al pintarlo)", count($r['json']['animos']) === 1 && $r['json']['animos'][0]['texto'] === "<b>¡Vamos!</b>");
	$pagina = peticion("andres", "GET", "index.php")['texto'];
	comprobar("En la página del servidor el HTML del ánimo sale escapado", str_contains($pagina, "&lt;b&gt;¡Vamos!&lt;/b&gt;"));
	$r = peticion("nerea", "POST", "api.php?accion=borrar_resultado", [], $tokenNerea);
	comprobar("Nerea quita su resultado", $r['json']['borrado'] === true);
	$r = peticion("nerea", "GET", "api.php?accion=estado&wod=1");
	comprobar("Quedan 2 resultados", count($r['json']['ranking']) === 2);

	echo "WOD nuevo\n";
	$r = peticion("nerea", "POST", "api.php?accion=wod", ["titulo" => "Murph", "descripcion" => "x", "tipo" => "tiempo"], $tokenNerea);
	comprobar("Un socio no puede publicar WOD: 403", $r['codigo'] === 403);
	$r = peticion("andres", "POST", "api.php?accion=wod", ["titulo" => "", "descripcion" => "", "tipo" => "nada"], $tokenAndres);
	comprobar("WOD vacío: 422 con 3 errores", $r['codigo'] === 422 && count($r['json']['errores']) === 3);
	$r = peticion("andres", "POST", "api.php?accion=wod", ["titulo" => "AMRAP 12", "descripcion" => "Máximas repeticiones de burpees y wall balls en 12 minutos", "tipo" => "repeticiones"], $tokenAndres);
	comprobar("El monitor publica un AMRAP: 201", $r['codigo'] === 201);
	$r = peticion("nerea", "GET", "api.php?accion=estado&wod=1&animos_desde=3");
	comprobar("El cliente con el WOD viejo recibe wod_nuevo y la pizarra vacía", $r['json']['wod_nuevo'] === true && $r['json']['wod']['tipo'] === "repeticiones" && count($r['json']['ranking']) === 0);
	$r = peticion("nerea", "POST", "api.php?accion=resultado", ["valor" => "6:00"], $tokenNerea);
	comprobar("Ahora un tiempo no vale: piden repeticiones", $r['codigo'] === 422);
	peticion("nerea", "POST", "api.php?accion=resultado", ["valor" => "143"], $tokenNerea);
	peticion("andres", "POST", "api.php?accion=resultado", ["valor" => "160"], $tokenAndres);
	$r = peticion("nerea", "GET", "api.php?accion=estado&wod=2");
	comprobar("Por repeticiones gana quien hace más", $r['json']['ranking'][0]['socio'] === "Andres Ruiz" && $r['json']['ranking'][1]['texto'] === "143 reps");

	echo "Salir\n";
	peticion("nerea", "GET", "salir.php");
	$r = peticion("nerea", "GET", "api.php?accion=estado");
	comprobar("Después de salir, 401", $r['codigo'] === 401);

	proc_terminate($servidor);
	proc_close($servidor);
	@unlink($baseDatos);
	foreach (glob(sys_get_temp_dir()."/velnora-pizarra-cookie-*-".getmypid()) as $archivo) {
		unlink($archivo);
	}

	echo "\n".$aciertos." correctas, ".$fallos." fallidas\n";
	exit($fallos === 0 ? 0 : 1);
?>
