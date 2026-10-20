<?php
	// Configuración de Velnora Hub: de dónde sale cada información y cuánto se guarda en caché.
	return [
		"datos" => __DIR__."/datos",
		"cache" => __DIR__."/cache",
		"aemet" => [
			"municipio" => "28079",	// Madrid
			"url" => "https://www.aemet.es/xml/municipios/localidad_28079.xml",
			"timeout" => 6,
			"ttl" => 3600	// la predicción se actualiza pocas veces al día
		],
		"ttl_afluencia" => 900,
		"ttl_rutas" => 86400
	];
?>
