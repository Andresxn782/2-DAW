<?php
	// Configuración central de Pulso (versión CSV)
	$config = [
		'nombre' => 'PULSO',
		'subtitulo' => 'Gimnasio Velnora · datos en CSV',
		'logo' => 'P',
		'color' => '#a855f7',
		'ruta_datos' => __DIR__.'/datos',
		'basededatos' => 'gimnasio',
		// Un CSV no guarda qué columna es la clave primaria, así que la indico aquí
		'claves_primarias' => [
			'socios' => 'Identificador', 'clases' => 'Identificador', 'entrenadores' => 'Identificador',
			'usuarios' => 'Identificador', 'roles' => 'Identificador',
			'planes' => 'id', 'reservas' => 'id', 'pagos' => 'id', 'material' => 'id'
		]
	];
?>
