<?php
	/**
	 * Configuración del diario de entrenamiento.
	 * 'almacen' decide dónde se guardan los datos: 'sqlite', 'mysql' o 'json'.
	 * El resto de la aplicación funciona igual con cualquiera de los tres.
	 */
	return [
		'almacen' => 'sqlite',
		'sqlite' => __DIR__.'/datos/diario.db',
		'mysql' => [
			'dsn' => 'mysql:host=localhost;dbname=velnora;charset=utf8mb4',
			'usuario' => 'root',
			'contrasena' => ''
		],
		'json' => __DIR__.'/datos/diario.json',
		'por_pagina' => 8
	];
?>
