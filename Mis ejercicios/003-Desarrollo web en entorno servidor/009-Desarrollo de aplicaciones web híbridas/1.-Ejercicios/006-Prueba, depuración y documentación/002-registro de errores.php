<?php
	// El usuario nunca debe ver un error interno; yo sí tengo que poder leerlo después.
	ini_set("display_errors", "0");
	ini_set("log_errors", "1");
	ini_set("error_log", __DIR__."/registro.log");

	// Los avisos de PHP (warnings) también los convierto en excepciones
	set_error_handler(function ($nivel, $mensaje, $archivo, $linea) {
		throw new ErrorException($mensaje, 0, $nivel, $archivo, $linea);
	});

	function leerTarifa($plan) {
		$tarifas = ["Musculación" => 39, "Yoga" => 35];
		return $tarifas[$plan];	// si el plan no existe, PHP avisa: "Undefined array key"
	}

	foreach (["Yoga", "Kárate"] as $plan) {
		try {
			echo "Tarifa de ".$plan.": ".leerTarifa($plan)." €<br>";
		} catch (Throwable $error) {
			error_log("Velnora: ".$error->getMessage()." en ".basename($error->getFile()).":".$error->getLine());
			echo "No hemos podido calcular la tarifa de ".htmlspecialchars($plan).". Ya lo estamos revisando.<br>";
		}
	}

	echo "<h2>Últimas líneas del registro</h2><pre>";
	$lineas = file_exists(__DIR__."/registro.log") ? file(__DIR__."/registro.log") : [];
	echo htmlspecialchars(implode("", array_slice($lineas, -5)));
	echo "</pre>";
?>
