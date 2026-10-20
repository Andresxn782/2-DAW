<?php
	// Configuración de la API de clases de Velnora.
	// La variable de entorno VELNORA_API_BD la usa pruebas.php para trabajar
	// con una base de datos aparte y no tocar la de verdad.
	return [
		"base_datos" => getenv("VELNORA_API_BD") ?: __DIR__."/datos/velnora-api.db",

		// Claves de acceso: cada programa que usa la API tiene la suya.
		// "app" puede consultar y reservar; "admin" además gestiona las clases.
		"claves" => [
			"velnora_app_2026" => ["nombre" => "App de socios", "permiso" => "app"],
			"velnora_admin_2026" => ["nombre" => "Recepción", "permiso" => "admin"]
		],

		"dias" => ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"]
	];
?>
