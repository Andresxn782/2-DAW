<?php
	// Carga las clases y prepara las fuentes. Lo usan api.php, index.php y pruebas.php.
	foreach (["Fuente", "Cache", "FuenteClases", "FuenteTiempo", "FuenteRutas", "FuenteAfluencia", "FuenteVideos"] as $clase) {
		require_once __DIR__."/Clases/".$clase.".php";
	}
	$config = require __DIR__."/config.php";
	$cache = new Cache($config['cache']);

	/**
	 * Devuelve ["datos", "desde_cache", "guardado"] de una fuente.
	 * Cada fuente tiene su caducidad: las de terceros y las pesadas se guardan más tiempo.
	 */
	function consultar(string $nombre, array $config, Cache $cache): array {
		switch ($nombre) {
			case "clases":
				// Cambia con la hora: no la guardo en caché
				$fuente = new FuenteClases($config['datos']);
				return ["datos" => $fuente->obtener(), "desde_cache" => false, "guardado" => date("c")];
			case "tiempo":
				return $cache->recordar("tiempo", $config['aemet']['ttl'], function () use ($config) {
					return (new FuenteTiempo($config['aemet'], $config['datos']))->obtener();
				});
			case "rutas":
				return $cache->recordar("rutas", $config['ttl_rutas'], function () use ($config) {
					return (new FuenteRutas($config['datos']))->obtener();
				});
			case "afluencia":
				// La clave lleva el día y la hora: la media cambia según cuándo se mire
				return $cache->recordar("afluencia-".date("N-G"), $config['ttl_afluencia'], function () use ($config) {
					return (new FuenteAfluencia($config['datos']))->obtener();
				});
			case "videos":
				// Mezcla dos fuentes: depende de lo que diga el tiempo de hoy
				$tiempo = consultar("tiempo", $config, $cache)['datos'];
				$fuera = $tiempo['dias'][0]['entrenar_fuera'] ?? false;
				return $cache->recordar("videos-".date("Ymd")."-".(int)$fuera, 86400, function () use ($config, $fuera) {
					return (new FuenteVideos($config['datos'], $fuera))->obtener();
				});
		}
		throw new InvalidArgumentException("Fuente desconocida: ".$nombre);
	}
?>
