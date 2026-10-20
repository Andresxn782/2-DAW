<?php
	// Pruebas de la API de clases de Velnora.
	// Uso:  php pruebas.php
	// Arranca un servidor de PHP con una base de datos temporal,
	// lanza peticiones HTTP reales y comprueba códigos y contenidos.

	$puerto = 8791;
	$baseDatos = sys_get_temp_dir()."/velnora-api-pruebas-".getmypid().".db";
	$base = "http://127.0.0.1:".$puerto;
	$enWindows = DIRECTORY_SEPARATOR === "\\";

	$entorno = getenv();
	$entorno["VELNORA_API_BD"] = $baseDatos;
	// Varios procesos para que el panel pueda llamar a la API a la vez (no existe en Windows)
	$entorno["PHP_CLI_SERVER_WORKERS"] = "3";
	$basura = $enWindows ? "NUL" : "/dev/null";
	$servidor = proc_open(
		[PHP_BINARY, "-S", "127.0.0.1:".$puerto, "-t", __DIR__],
		[1 => ["file", $basura, "w"], 2 => ["file", $basura, "w"]],
		$tuberias, __DIR__, $entorno
	);
	if ($servidor === false) {
		exit("No he podido arrancar el servidor de pruebas\n");
	}
	usleep(700000);

	function peticion($metodo, $ruta, $clave = "velnora_admin_2026", $cuerpo = null, $cabecerasExtra = []) {
		global $base;
		$curl = curl_init($base."/api/index.php".$ruta);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $metodo);
		$cabeceras = $cabecerasExtra;
		if ($clave !== null) {
			$cabeceras[] = "X-API-Key: ".$clave;
		}
		if ($cuerpo !== null) {
			$cabeceras[] = "Content-Type: application/json";
			curl_setopt($curl, CURLOPT_POSTFIELDS, is_string($cuerpo) ? $cuerpo : json_encode($cuerpo));
		}
		curl_setopt($curl, CURLOPT_HTTPHEADER, $cabeceras);
		$texto = (string)curl_exec($curl);
		$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$tamanoCabeceras = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		curl_close($curl);
		$cuerpoRespuesta = substr($texto, $tamanoCabeceras);
		return [
			"codigo" => $codigo,
			"cabeceras" => substr($texto, 0, $tamanoCabeceras),
			"texto" => $cuerpoRespuesta,
			"json" => json_decode($cuerpoRespuesta, true)
		];
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

	echo "Estado y autenticación\n";
	$r = peticion("GET", "/estado", null);
	comprobar("/estado responde 200 sin clave", $r['codigo'] === 200 && $r['json']['servicio'] === "API de clases de Velnora");
	$r = peticion("GET", "/clases", null);
	comprobar("Sin clave: 401", $r['codigo'] === 401 && $r['json']['error'] === true);
	$r = peticion("GET", "/clases", "inventada");
	comprobar("Clave falsa: 401", $r['codigo'] === 401);
	comprobar("La respuesta es JSON en UTF-8", str_contains(strtolower($r['cabeceras']), "content-type: application/json; charset=utf-8"));
	$r = peticion("OPTIONS", "/clases", null);
	comprobar("OPTIONS (CORS) responde 204 con Access-Control-Allow-Origin", $r['codigo'] === 204 && str_contains($r['cabeceras'], "Access-Control-Allow-Origin: *"));

	echo "Consultar clases\n";
	$r = peticion("GET", "/clases", "velnora_app_2026");
	comprobar("La app lista 6 clases", $r['codigo'] === 200 && $r['json']['total'] === 6);
	comprobar("Ordenadas de lunes a sábado", $r['json']['clases'][0]['dia'] === "Lunes" && $r['json']['clases'][5]['dia'] === "Sábado");
	comprobar("Los números son enteros en el JSON", $r['json']['clases'][0]['aforo'] === 20 && $r['json']['clases'][0]['ocupadas'] === 2);
	$r = peticion("GET", "/clases?dia=".urlencode("Miércoles"));
	comprobar("Filtro por día", $r['json']['total'] === 1 && $r['json']['clases'][0]['nombre'] === "Yoga");
	$r = peticion("GET", "/clases?monitor=marta");
	comprobar("Filtro por monitor (sin distinguir mayúsculas)", $r['json']['total'] === 2);
	$r = peticion("GET", "/clases/4");
	comprobar("Pilates tiene 1 plaza libre", $r['codigo'] === 200 && $r['json']['libres'] === 1);
	$r = peticion("GET", "/clases/99");
	comprobar("Clase inexistente: 404", $r['codigo'] === 404);
	$r = peticion("GET", "/clases/abc");
	comprobar("Id que no es número: 404", $r['codigo'] === 404);
	$r = peticion("GET", "/inventado");
	comprobar("Ruta desconocida: 404", $r['codigo'] === 404);
	$r = peticion("PATCH", "/clases");
	comprobar("Método no permitido: 405 con cabecera Allow", $r['codigo'] === 405 && str_contains($r['cabeceras'], "Allow:"));
	$r = peticion("GET", "/clases/1?formato=xml");
	$xml = @simplexml_load_string($r['texto']);
	comprobar("?formato=xml devuelve XML válido", $xml !== false && (string)$xml->nombre === "Spinning");
	$r = peticion("GET", "/clases/1", "velnora_admin_2026", null, ["Accept: application/xml"]);
	comprobar("Accept: application/xml también da XML", str_contains($r['cabeceras'], "application/xml"));
	$curl = curl_init($base."/api/index.php?ruta=/clases/2");
	curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["X-API-Key: velnora_app_2026"]]);
	$crossfit = json_decode(curl_exec($curl), true);
	curl_close($curl);
	comprobar("La forma ?ruta= también funciona", ($crossfit['nombre'] ?? "") === "Crossfit");

	echo "Gestionar clases\n";
	$nueva = ["nombre" => "Zumba", "monitor" => "Andres Ruiz", "dia" => "Viernes", "hora" => "17:00", "duracion" => 45, "aforo" => 2];
	$r = peticion("POST", "/clases", "velnora_app_2026", $nueva);
	comprobar("La app no puede crear clases: 403", $r['codigo'] === 403);
	$r = peticion("POST", "/clases", "velnora_admin_2026", $nueva);
	comprobar("Admin crea una clase: 201", $r['codigo'] === 201 && $r['json']['nombre'] === "Zumba" && $r['json']['libres'] === 2);
	comprobar("Con cabecera Location", str_contains($r['cabeceras'], "Location: index.php/clases/".$r['json']['id']));
	$zumba = $r['json']['id'];
	$r = peticion("POST", "/clases", "velnora_admin_2026", ["nombre" => "", "dia" => "Festivo", "hora" => "25:00", "duracion" => 5, "aforo" => 100]);
	comprobar("Datos no válidos: 422 con un error por campo", $r['codigo'] === 422 && count($r['json']['errores']) === 6);
	$r = peticion("POST", "/clases", "velnora_admin_2026", "{esto no es json");
	comprobar("JSON roto: 400", $r['codigo'] === 400);
	$r = peticion("PUT", "/clases/".$zumba, "velnora_admin_2026", ["hora" => "17:30"]);
	comprobar("PUT cambia solo la hora", $r['codigo'] === 200 && $r['json']['hora'] === "17:30" && $r['json']['nombre'] === "Zumba");
	$r = peticion("PUT", "/clases/".$zumba, "velnora_admin_2026", []);
	comprobar("PUT sin campos: 400", $r['codigo'] === 400);

	echo "Reservas\n";
	$r = peticion("POST", "/clases/".$zumba."/reservas", "velnora_app_2026", ["socio_id" => 1]);
	comprobar("La app reserva una plaza: 201", $r['codigo'] === 201 && $r['json']['clase']['libres'] === 1);
	$reservaLucia = $r['json']['id'];
	$r = peticion("POST", "/clases/".$zumba."/reservas", "velnora_app_2026", ["socio_id" => 1]);
	comprobar("El mismo socio otra vez: 409", $r['codigo'] === 409);
	$r = peticion("POST", "/clases/".$zumba."/reservas", "velnora_app_2026", ["socio_id" => 2]);
	comprobar("Segunda plaza: 201 y la clase se llena", $r['codigo'] === 201 && $r['json']['clase']['libres'] === 0);
	$r = peticion("POST", "/clases/".$zumba."/reservas", "velnora_app_2026", ["socio_id" => 3]);
	comprobar("Clase completa: 409", $r['codigo'] === 409 && str_contains($r['json']['mensaje'], "completa"));
	$r = peticion("POST", "/clases/".$zumba."/reservas", "velnora_app_2026", ["socio_id" => 99]);
	comprobar("Socio inexistente: 404", $r['codigo'] === 404);
	$r = peticion("POST", "/clases/".$zumba."/reservas", "velnora_app_2026", []);
	comprobar("Sin socio_id: 422", $r['codigo'] === 422);
	$r = peticion("PUT", "/clases/".$zumba, "velnora_admin_2026", ["aforo" => 1]);
	comprobar("Bajar el aforo por debajo de las reservas: 409", $r['codigo'] === 409);
	$r = peticion("GET", "/clases/".$zumba."/reservas", "velnora_app_2026");
	comprobar("Lista de reservas con el nombre del socio", count($r['json']['reservas']) === 2 && $r['json']['reservas'][0]['socio'] === "Lucía Martín");
	$r = peticion("DELETE", "/reservas/".$reservaLucia, "velnora_app_2026");
	comprobar("Cancelar reserva: 200", $r['codigo'] === 200);
	$r = peticion("DELETE", "/reservas/".$reservaLucia, "velnora_app_2026");
	comprobar("Cancelarla otra vez: 404", $r['codigo'] === 404);
	$r = peticion("GET", "/socios", "velnora_app_2026");
	comprobar("Lista de 6 socios", $r['json']['total'] === 6);

	echo "Borrar\n";
	$r = peticion("DELETE", "/clases/".$zumba, "velnora_app_2026");
	comprobar("La app no puede borrar: 403", $r['codigo'] === 403);
	$r = peticion("DELETE", "/clases/".$zumba);
	comprobar("Admin borra la clase y anula 1 reserva", $r['codigo'] === 200 && $r['json']['reservas_anuladas'] === 1);
	$pdo = new PDO("sqlite:".$baseDatos);
	comprobar("No quedan reservas huérfanas", (int)$pdo->query("SELECT COUNT(*) FROM reservas WHERE clase_id = ".$zumba)->fetchColumn() === 0);
	$pdo = null;

	echo "Documentación y panel\n";
	$documentacion = file_get_contents($base."/docs/");
	comprobar("La documentación lista las 10 operaciones", substr_count((string)$documentacion, "<details>") === 10);
	if ($enWindows) {
		echo "  (en Windows el servidor de PHP atiende de uno en uno; el panel se prueba en XAMPP)\n";
	} else {
		$panel = file_get_contents($base."/cliente/");
		comprobar("El panel muestra las clases que le da la API", str_contains((string)$panel, "Spinning") && str_contains((string)$panel, "1 libres de 3"));
	}

	proc_terminate($servidor);
	proc_close($servidor);
	@unlink($baseDatos);

	echo "\n".$aciertos." correctas, ".$fallos." fallidas\n";
	exit($fallos === 0 ? 0 : 1);
?>
